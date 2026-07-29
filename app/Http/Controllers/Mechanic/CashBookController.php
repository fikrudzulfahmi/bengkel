<?php

namespace App\Http\Controllers\Mechanic;

use App\Http\Controllers\Controller;
use App\Models\CashBook;
use App\Models\ActivityLog;
use App\Models\TreasurerDeposit;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class CashBookController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $userId = auth()->id();

        // Kas mekanik sendiri
        $cashBooks = CashBook::bengkel()
            ->where('user_id', $userId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $totalPemasukan = $cashBooks->where('type', 'pemasukan')->sum('amount');
        $totalPengeluaran = $cashBooks->where('type', 'pengeluaran')->sum('amount');

        // Kas bendahara (read-only untuk mekanik)
        $bendaharaCashBooks = CashBook::bendahara()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $bendaharaPemasukan = $bendaharaCashBooks->where('type', 'pemasukan')->sum('amount');
        $bendaharaPengeluaran = $bendaharaCashBooks->where('type', 'pengeluaran')->sum('amount');

        // Setoran yang masih pending
        $pendingDeposits = TreasurerDeposit::where('mechanic_id', $userId)
            ->where('status', 'pending')
            ->latest()
            ->get();

        // History setoran
        $depositHistory = TreasurerDeposit::where('mechanic_id', $userId)
            ->whereIn('status', ['approved', 'rejected'])
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->latest()
            ->get();

        return Inertia::render('Mechanic/CashBooks/Index', [
            'cashBooks' => $cashBooks,
            'summary' => [
                'pemasukan' => $totalPemasukan,
                'pengeluaran' => $totalPengeluaran,
                'saldo' => $totalPemasukan - $totalPengeluaran
            ],
            'bendaharaCashBooks' => $bendaharaCashBooks,
            'bendaharaSummary' => [
                'pemasukan' => $bendaharaPemasukan,
                'pengeluaran' => $bendaharaPengeluaran,
                'saldo' => $bendaharaPemasukan - $bendaharaPengeluaran,
            ],
            'filters' => [
                'month' => $month,
                'year' => $year
            ],
            'pendingDeposits' => $pendingDeposits,
            'depositHistory' => $depositHistory,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:pemasukan,pengeluaran',
            'amount' => 'required|integer|min:1',
            'description' => 'required|string',
            'date' => 'required|date',
        ]);

        CashBook::create([
            'user_id' => auth()->id(),
            'category' => 'bengkel',
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'date' => $request->date,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Catat Buku Kas',
            'description' => "Mencatat {$request->type} sebesar Rp" . number_format($request->amount, 0, ',', '.') . " untuk: {$request->description}"
        ]);

        return back()->with('success', 'Data buku kas berhasil disimpan.');
    }

    public function destroy(CashBook $cashBook)
    {
        $desc = $cashBook->description;
        $cashBook->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Hapus Catatan Kas',
            'description' => "Menghapus catatan kas: {$desc}"
        ]);

        return back()->with('success', 'Catatan buku kas berhasil dihapus.');
    }

    /**
     * Mekanik mengajukan setoran ke bendahara (status: pending)
     */
    public function setorBendahara(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
            'description' => 'required|string',
            'date' => 'required|date',
        ]);

        TreasurerDeposit::create([
            'mechanic_id' => auth()->id(),
            'amount' => $request->amount,
            'description' => $request->description,
            'date' => $request->date,
            'status' => 'pending',
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Ajukan Setoran ke Bendahara',
            'description' => "Mengajukan setoran ke bendahara sebesar Rp" . number_format($request->amount, 0, ',', '.') . ": {$request->description}"
        ]);

        return back()->with('success', 'Pengajuan setoran berhasil dikirim. Menunggu verifikasi bendahara.');
    }
}
