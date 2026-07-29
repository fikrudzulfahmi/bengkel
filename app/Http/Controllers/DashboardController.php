<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\CashBook;
use App\Models\Transaction;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Sparepart;
use App\Models\TreasurerDeposit;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function admin()
    {
        $totalPemasukan = CashBook::where('type', 'pemasukan')->sum('amount');
        $totalPengeluaran = CashBook::where('type', 'pengeluaran')->sum('amount');
        $totalRevenue = $totalPemasukan - $totalPengeluaran;

        $totalMechanics = User::where('role', 'mekanik')->count();
        $totalTransactions = Transaction::count();

        $recentActivities = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'metrics' => [
                'total_revenue' => $totalRevenue,
                'total_mechanics' => $totalMechanics,
                'total_transactions' => $totalTransactions,
            ],
            'recentActivities' => $recentActivities
        ]);
    }

    public function mechanic()
    {
        $today = Carbon::today();
        $userId = auth()->id();

        $todayPemasukan = CashBook::where('user_id', $userId)
            ->where('type', 'pemasukan')
            ->whereDate('date', $today)
            ->sum('amount');

        $todayPengeluaran = CashBook::where('user_id', $userId)
            ->where('type', 'pengeluaran')
            ->whereDate('date', $today)
            ->sum('amount');

        $todayRevenue = $todayPemasukan - $todayPengeluaran;

        $todayTransactions = Transaction::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->count();

        $lowStockSpareparts = Sparepart::where('stock', '<', 5)
            ->orderBy('stock', 'asc')
            ->get();

        $recentActivities = ActivityLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        return Inertia::render('Mechanic/Dashboard', [
            'metrics' => [
                'today_revenue' => $todayRevenue,
                'today_transactions' => $todayTransactions,
            ],
            'lowStockSpareparts' => $lowStockSpareparts,
            'recentActivities' => $recentActivities
        ]);
    }

    public function bendahara()
    {
        $month = date('m');
        $year = date('Y');

        $totalPemasukan = CashBook::bendahara()
            ->where('type', 'pemasukan')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('amount');

        $totalPengeluaran = CashBook::bendahara()
            ->where('type', 'pengeluaran')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('amount');

        $pendingDeposits = TreasurerDeposit::pending()->with('mechanic')->latest()->get();

        $recentActivities = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        return Inertia::render('Bendahara/Dashboard', [
            'metrics' => [
                'pemasukan_bulan' => $totalPemasukan,
                'pengeluaran_bulan' => $totalPengeluaran,
                'saldo_bulan' => $totalPemasukan - $totalPengeluaran,
                'pending_deposits' => $pendingDeposits->count(),
            ],
            'pendingDeposits' => $pendingDeposits,
            'recentActivities' => $recentActivities,
        ]);
    }
}
