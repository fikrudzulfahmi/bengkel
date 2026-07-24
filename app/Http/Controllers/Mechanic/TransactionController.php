<?php

namespace App\Http\Controllers\Mechanic;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\TransactionService;
use App\Models\Customer;
use App\Models\Motorcycle;
use App\Models\Sparepart;
use App\Models\Service;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['customer', 'motorcycle'])
            ->where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->paginate(10);
            
        return Inertia::render('Mechanic/Transactions/Index', [
            'transactions' => $transactions
        ]);
    }

    public function create()
    {
        $customers = Customer::with('motorcycles')->orderBy('name')->get();
        $spareparts = Sparepart::orderBy('name')->get();
        $services = Service::orderBy('name')->get();
        
        return Inertia::render('Mechanic/Transactions/Create', [
            'customers' => $customers,
            'spareparts' => $spareparts,
            'services' => $services
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required_without:customer_id|string|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'motorcycle_plate' => 'required_without:motorcycle_id|string|max:255',
            'motorcycle_type' => 'required_without:motorcycle_id|string|max:255',
            'discount' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'items' => 'array', // Spareparts
            'items.*.sparepart_id' => 'required_without:items.*.is_new|integer',
            'items.*.qty' => 'required|integer|min:1',
            'services' => 'array', // Services
            'services.*.service_id' => 'required_without:services.*.is_new|integer',
        ]);

        DB::beginTransaction();
        try {
            // Handle Customer (Quick Add)
            $customer_id = $request->customer_id;
            if (!$customer_id) {
                $customer = Customer::create([
                    'name' => $request->customer_name,
                    'phone' => $request->customer_phone,
                ]);
                $customer_id = $customer->id;
            }

            // Handle Motorcycle (Quick Add)
            $motorcycle_id = $request->motorcycle_id;
            if (!$motorcycle_id) {
                $motorcycle = Motorcycle::create([
                    'customer_id' => $customer_id,
                    'plate_number' => $request->motorcycle_plate,
                    'type' => $request->motorcycle_type,
                ]);
                $motorcycle_id = $motorcycle->id;
            }

            $totalPrice = 0;
            $discount = $request->discount ?? 0;

            // Create Transaction
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'customer_id' => $customer_id,
                'motorcycle_id' => $motorcycle_id,
                'discount' => $discount,
                'status' => 'selesai',
                'notes' => $request->notes,
                'total_price' => 0, // di-update nanti
            ]);

            // Handle Spareparts & Stock (Including Quick Add)
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $item) {
                    if (isset($item['is_new']) && $item['is_new']) {
                        // Quick Add Sparepart
                        $sparepart = Sparepart::create([
                            'name' => $item['new_name'],
                            'price' => $item['new_price'],
                            'buy_price' => $item['new_buy_price'],
                            'stock' => $item['new_stock'] // Stok awal dari input quick add
                        ]);
                        ActivityLog::create([
                            'user_id' => auth()->id(),
                            'action' => 'Tambah Sparepart',
                            'description' => "Quick Add sparepart baru: {$sparepart->name} ({$sparepart->stock} pcs) dari halaman Kasir."
                        ]);
                    } else {
                        $sparepart = Sparepart::findOrFail($item['sparepart_id']);
                        
                        if (isset($item['is_restock']) && $item['is_restock']) {
                            $sparepart->increment('stock', $item['restock_qty']);
                            
                            $restockTotal = $item['new_buy_price'] * $item['restock_qty'];
                            if ($restockTotal > 0) {
                                \App\Models\CashBook::create([
                                    'user_id' => auth()->id(),
                                    'type' => 'pengeluaran',
                                    'amount' => $restockTotal,
                                    'description' => "Kulakan stok '{$sparepart->name}' (Quick Restock Kasir). Sebanyak: {$item['restock_qty']} pcs.",
                                    'date' => now()->toDateString(),
                                ]);
                            }
                            
                            ActivityLog::create([
                                'user_id' => auth()->id(),
                                'action' => 'Kulakan Sparepart',
                                'description' => "Quick Restock '{$sparepart->name}' sebanyak {$item['restock_qty']} pcs dari Kasir."
                            ]);
                            
                            $sparepart->update(['buy_price' => $item['new_buy_price']]);
                        }
                    }
                    
                    if ($sparepart->stock < $item['qty']) {
                        throw new \Exception("Stok {$sparepart->name} tidak mencukupi. (Sisa: {$sparepart->stock})");
                    }

                    $sparepart->decrement('stock', $item['qty']);

                    $subtotal = $sparepart->price * $item['qty'];
                    $totalPrice += $subtotal;

                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'sparepart_id' => $sparepart->id,
                        'qty' => $item['qty'],
                        'price' => $sparepart->price,
                    ]);
                }
            }

            // Handle Services (Including Quick Add)
            if ($request->has('services') && is_array($request->services)) {
                foreach ($request->services as $svcItem) {
                    if (isset($svcItem['is_new']) && $svcItem['is_new']) {
                        // Quick Add Service
                        $service = Service::create([
                            'name' => $svcItem['new_name'],
                            'price' => $svcItem['new_price']
                        ]);
                    } else {
                        $service = Service::findOrFail($svcItem['service_id']);
                    }

                    $totalPrice += $service->price;

                    TransactionService::create([
                        'transaction_id' => $transaction->id,
                        'service_id' => $service->id,
                        'price' => $service->price,
                    ]);
                }
            }

            // Hitung grand total
            $grandTotal = $totalPrice - $discount;
            if ($grandTotal < 0) $grandTotal = 0;

            $transaction->update(['total_price' => $grandTotal]);

            $plate = $request->motorcycle_plate ?? 'baru';
            
            // Masukkan ke Buku Kas sebagai Pemasukan
            \App\Models\CashBook::create([
                'user_id' => auth()->id(),
                'type' => 'pemasukan',
                'amount' => $grandTotal,
                'description' => "Pendapatan Servis & Sparepart (Nota: #TRX-" . str_pad($transaction->id, 6, '0', STR_PAD_LEFT) . ")",
                'date' => now()->toDateString(),
            ]);

            // Masukkan ke Jurnal Harian
            \App\Models\Journal::create([
                'user_id' => auth()->id(),
                'date' => now()->toDateString(),
                'activity' => "Menyelesaikan transaksi servis plat {$plate} dengan total Rp" . number_format($grandTotal, 0, ',', '.'),
            ]);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Transaksi Baru',
                'description' => "Membuat transaksi servis untuk plat {$plate}. Total: Rp" . number_format($grandTotal, 0, ',', '.')
            ]);

            DB::commit();
            return back()->with('success', 'Transaksi berhasil disimpan.')->with('transaction_id', $transaction->id);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function print(Transaction $transaction)
    {
        $transaction->load(['customer', 'motorcycle', 'user', 'details.sparepart', 'transactionServices.service']);
        return Inertia::render('Mechanic/Transactions/Print', [
            'transaction' => $transaction
        ]);
    }
}
