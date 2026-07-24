<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\CashBook;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Fitur Laporan Keuangan (Pemasukan, Pengeluaran, Laba)
        // Default filter bulan ini
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $transactions = Transaction::with(['customer', 'motorcycle', 'user'])
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', 'selesai')
            ->latest()
            ->get();

        $cashBooks = CashBook::with('user')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->latest()
            ->get();

        $totalIncomeServis = $transactions->sum('total_price');
        $totalIncomeLain = $cashBooks->where('type', 'pemasukan')->sum('amount');
        $totalPengeluaran = $cashBooks->where('type', 'pengeluaran')->sum('amount');

        return Inertia::render('Admin/Reports/Index', [
            'transactions' => $transactions,
            'cashBooks' => $cashBooks,
            'summary' => [
                'income_servis' => $totalIncomeServis,
                'income_lain' => $totalIncomeLain,
                'pengeluaran' => $totalPengeluaran,
                'laba' => ($totalIncomeServis + $totalIncomeLain) - $totalPengeluaran
            ],
            'filters' => [
                'month' => $month,
                'year' => $year
            ]
        ]);
    }
}
