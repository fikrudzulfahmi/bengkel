<?php

namespace App\Http\Controllers\Mechanic;

use App\Http\Controllers\Controller;
use App\Models\Sparepart;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SparepartController extends Controller
{
    public function index()
    {
        $spareparts = Sparepart::orderBy('name')->get();
        return Inertia::render('Mechanic/Spareparts/Index', [
            'spareparts' => $spareparts
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'price' => 'required|integer|min:0',
            'buy_price' => 'nullable|integer|min:0',
        ]);

        $sparepart = Sparepart::create($request->only(['name', 'stock', 'price', 'buy_price']));

        if ($sparepart->stock > 0 && $sparepart->buy_price > 0) {
            $total = $sparepart->stock * $sparepart->buy_price;
            \App\Models\CashBook::create([
                'user_id' => auth()->id(),
                'type' => 'pengeluaran',
                'amount' => $total,
                'description' => "Pengadaan awal sparepart baru: {$sparepart->name} ({$sparepart->stock} pcs)",
                'date' => now()->toDateString(),
            ]);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Tambah Sparepart',
            'description' => "Menambahkan sparepart baru: {$sparepart->name} ({$sparepart->stock} pcs)"
        ]);

        return back()->with('success', 'Sparepart berhasil ditambahkan.');
    }

    public function update(Request $request, Sparepart $sparepart)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'price' => 'required|integer|min:0',
            'buy_price' => 'nullable|integer|min:0',
        ]);

        if ($request->has('is_restock') && $request->is_restock) {
            $qty = $request->restock_qty;
            $sparepart->increment('stock', $qty);
            $sparepart->update([
                'buy_price' => $request->buy_price ?? $sparepart->buy_price
            ]);
            
            $total = $qty * $sparepart->buy_price;
            if ($total > 0) {
                \App\Models\CashBook::create([
                    'user_id' => auth()->id(),
                    'type' => 'pengeluaran',
                    'amount' => $total,
                    'description' => "Kulakan tambahan stok: {$sparepart->name} ({$qty} pcs)",
                    'date' => now()->toDateString(),
                ]);
            }
            
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Kulakan Sparepart',
                'description' => "Menambah stok {$sparepart->name} sebanyak {$qty} pcs melalui menu Manajemen."
            ]);
            
            return back()->with('success', 'Stok berhasil ditambahkan dan dicatat di Buku Kas.');
        } else {
            $oldStock = $sparepart->stock;
            $sparepart->update($request->only(['name', 'price', 'buy_price', 'stock']));

            if ($oldStock != $sparepart->stock) {
                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'Koreksi Stok',
                    'description' => "Koreksi manual stok {$sparepart->name} dari {$oldStock} menjadi {$sparepart->stock}"
                ]);
            }
            
            return back()->with('success', 'Data sparepart berhasil diperbarui.');
        }
    }

    public function destroy(Sparepart $sparepart)
    {
        $name = $sparepart->name;
        $sparepart->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Hapus Sparepart',
            'description' => "Menghapus sparepart: {$name}"
        ]);

        return back()->with('success', 'Sparepart berhasil dihapus.');
    }
}
