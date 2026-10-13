<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\CashBook;
use App\Models\TreasurerDeposit;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $cashBooks = CashBook::bendahara()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date')
            ->get();

        $pemasukan = $cashBooks->where('type', 'pemasukan');
        $pengeluaran = $cashBooks->where('type', 'pengeluaran');

        return Inertia::render('Bendahara/Reports/Index', [
            'summary' => [
                'pemasukan' => $pemasukan->sum('amount'),
                'pengeluaran' => $pengeluaran->sum('amount'),
                'saldo' => $pemasukan->sum('amount') - $pengeluaran->sum('amount'),
            ],
            'filters' => ['month' => $month, 'year' => $year],
        ]);
    }

    public function printFinance(Request $request)
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

        // Setoran yang diapprove (untuk rincian sumber pemasukan)
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
