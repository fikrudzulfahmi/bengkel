<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\TreasurerDeposit;
use App\Models\CashBook;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class DepositController extends Controller
{
    /**
     * Daftar setoran yang perlu diverifikasi bendahara
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $depositsQuery = TreasurerDeposit::with('mechanic')
            ->where('status', $status);

        if ($status !== 'pending') {
            $depositsQuery->whereMonth('date', $month)->whereYear('date', $year);
        }

        $deposits = $depositsQuery->latest()->get();

        $pendingCount = TreasurerDeposit::pending()->count();

        return Inertia::render('Bendahara/Deposits/Index', [
            'deposits' => $deposits,
            'pendingCount' => $pendingCount,
            'filters' => [
                'status' => $status,
                'month' => $month,
                'year' => $year,
            ],
        ]);
    }

    /**
     * Approve setoran: buat entry di cash_books mekanik & bendahara
     */
    public function approve(TreasurerDeposit $deposit)
    {
        if ($deposit->status !== 'pending') {
            return back()->with('error', 'Setoran ini sudah diproses.');
        }

        // Buat pengeluaran di kas mekanik
        $mechanicCashBook = CashBook::create([
            'user_id' => $deposit->mechanic_id,
            'category' => 'bengkel',
            'type' => 'pengeluaran',
            'amount' => $deposit->amount,
            'description' => "Setor ke Bendahara: {$deposit->description}",
            'date' => $deposit->date,
        ]);

        // Buat pemasukan di kas bendahara
        $mechanic = $deposit->mechanic;
        $treasurerCashBook = CashBook::create([
            'user_id' => auth()->id(),
            'category' => 'bendahara',
            'type' => 'pemasukan',
            'amount' => $deposit->amount,
            'description' => "Setoran dari {$mechanic->name}: {$deposit->description}",
            'date' => $deposit->date,
        ]);

        // Update deposit
        $deposit->update([
            'status' => 'approved',
            'treasurer_id' => auth()->id(),
            'mechanic_cash_book_id' => $mechanicCashBook->id,
            'treasurer_cash_book_id' => $treasurerCashBook->id,
            'processed_at' => now(),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Approve Setoran',
            'description' => "Menyetujui setoran dari {$mechanic->name} sebesar Rp" . number_format($deposit->amount, 0, ',', '.')
        ]);

        return back()->with('success', "Setoran dari {$mechanic->name} berhasil disetujui.");
    }

    /**
     * Tolak setoran: tidak ada perubahan di kas
     */
    public function reject(Request $request, TreasurerDeposit $deposit)
    {
        $request->validate([
            'rejection_note' => 'nullable|string|max:500',
        ]);

        if ($deposit->status !== 'pending') {
            return back()->with('error', 'Setoran ini sudah diproses.');
        }

        $mechanic = $deposit->mechanic;

        $deposit->update([
            'status' => 'rejected',
            'treasurer_id' => auth()->id(),
            'rejection_note' => $request->rejection_note,
            'processed_at' => now(),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Tolak Setoran',
            'description' => "Menolak setoran dari {$mechanic->name} sebesar Rp" . number_format($deposit->amount, 0, ',', '.')
        ]);

        return back()->with('success', "Setoran dari {$mechanic->name} berhasil ditolak.");
    }
}
