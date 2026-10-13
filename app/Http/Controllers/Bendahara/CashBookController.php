<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\CashBook;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CashBookController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $cashBooks = CashBook::bendahara()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->with('user')
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $totalPemasukan = $cashBooks->where('type', 'pemasukan')->sum('amount');
        $totalPengeluaran = $cashBooks->where('type', 'pengeluaran')->sum('amount');

        return Inertia::render('Bendahara/CashBooks/Index', [
            'cashBooks' => $cashBooks,
            'summary' => [
                'pemasukan' => $totalPemasukan,
                'pengeluaran' => $totalPengeluaran,
                'saldo' => $totalPemasukan - $totalPengeluaran
            ],
            'filters' => [
                'month' => $month,
                'year' => $year
            ],
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
            'category' => 'bendahara',
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'date' => $request->date,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Catat Kas Bendahara',
            'description' => "Mencatat {$request->type} di kas bendahara sebesar Rp" . number_format($request->amount, 0, ',', '.') . ": {$request->description}"
        ]);

        return back()->with('success', 'Data kas bendahara berhasil disimpan.');
    }

    public function destroy(CashBook $cashBook)
    {
        $desc = $cashBook->description;
        $cashBook->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Hapus Catatan Kas Bendahara',
            'description' => "Menghapus catatan kas bendahara: {$desc}"
        ]);

        return back()->with('success', 'Catatan kas berhasil dihapus.');
    }
}
