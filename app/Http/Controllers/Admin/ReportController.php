<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\CashBook;
use App\Models\TreasurerDeposit;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

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
        $pemasukanLain = $cashBooks->filter(function($cb) {
            return $cb->type === 'pemasukan' && !str_starts_with($cb->description, 'Pendapatan Servis & Sparepart');
        });
        $totalIncomeLain = $pemasukanLain->sum('amount');
        $totalPengeluaran = $cashBooks->where('type', 'pengeluaran')->sum('amount');

        $bendaharaCashBooks = CashBook::bendahara()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();
        
        $bendaharaPemasukan = $bendaharaCashBooks->where('type', 'pemasukan')->sum('amount');
        $bendaharaPengeluaran = $bendaharaCashBooks->where('type', 'pengeluaran')->sum('amount');

        return Inertia::render('Admin/Reports/Index', [
            'transactions' => $transactions,
            'cashBooks' => $cashBooks,
            'summary' => [
                'income_servis' => $totalIncomeServis,
                'income_lain' => $totalIncomeLain,
                'pengeluaran' => $totalPengeluaran,
                'laba' => ($totalIncomeServis + $totalIncomeLain) - $totalPengeluaran
            ],
            'bendaharaSummary' => [
                'pemasukan' => $bendaharaPemasukan,
                'pengeluaran' => $bendaharaPengeluaran,
                'saldo' => $bendaharaPemasukan - $bendaharaPengeluaran
            ],
            'filters' => [
                'month' => $month,
                'year' => $year
            ]
        ]);
    }

    public function printService(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $transactions = Transaction::with(['customer', 'motorcycle', 'user', 'transactionServices.service', 'details.sparepart'])
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', 'selesai')
            ->latest()
            ->get();

        return view('admin.reports.print-service', compact('transactions', 'month', 'year'));
    }

    public function printFinance(Request $request)
    {
        $type = $request->get('type', 'monthly');
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $queryTransactions = Transaction::where('status', 'selesai');
        $queryCashBooks = CashBook::query();

        if ($type === 'monthly') {
            $queryTransactions->whereMonth('created_at', $month)->whereYear('created_at', $year);
            $queryCashBooks->whereMonth('date', $month)->whereYear('date', $year);
        } else {
            $queryTransactions->whereYear('created_at', $year);
            $queryCashBooks->whereYear('date', $year);
        }

        $transactions = $queryTransactions->get();
        $cashBooks = $queryCashBooks->get();

        $totalIncomeServis = $transactions->sum('total_price');
        $pemasukanLain = $cashBooks->filter(function($cb) {
            return $cb->type === 'pemasukan' && !str_starts_with($cb->description, 'Pendapatan Servis & Sparepart');
        });
        $totalIncomeLain = $pemasukanLain->sum('amount');
        $totalPengeluaran = $cashBooks->where('type', 'pengeluaran')->sum('amount');
        $laba = ($totalIncomeServis + $totalIncomeLain) - $totalPengeluaran;

        return view('admin.reports.print-finance', compact(
            'transactions', 'cashBooks', 'pemasukanLain', 'type', 'month', 'year',
            'totalIncomeServis', 'totalIncomeLain', 'totalPengeluaran', 'laba'
        ));
    }

    public function printBendahara(Request $request)
    {
        $type = $request->get('type', 'monthly');
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $query = CashBook::bendahara()->orderBy('date');

        if ($type === 'monthly') {
            $query->whereMonth('date', $month)->whereYear('date', $year);
        } else {
            $query->whereYear('date', $year);
        }

        $cashBooks = $query->get();

        $depositQuery = TreasurerDeposit::with('mechanic')->where('status', 'approved');
        if ($type === 'monthly') {
            $depositQuery->whereMonth('date', $month)->whereYear('date', $year);
        } else {
            $depositQuery->whereYear('date', $year);
        }
        $approvedDeposits = $depositQuery->get();

        $pemasukan = $cashBooks->where('type', 'pemasukan');
        $pengeluaran = $cashBooks->where('type', 'pengeluaran');

        $totalPemasukan = $pemasukan->sum('amount');
        $totalPengeluaran = $pengeluaran->sum('amount');
        $saldo = $totalPemasukan - $totalPengeluaran;

        return view('bendahara.reports.print-finance', compact(
            'cashBooks', 'pemasukan', 'pengeluaran', 'approvedDeposits',
            'type', 'month', 'year',
            'totalPemasukan', 'totalPengeluaran', 'saldo'
        ));
    }
}
