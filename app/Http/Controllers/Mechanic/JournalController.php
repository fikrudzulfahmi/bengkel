<?php

namespace App\Http\Controllers\Mechanic;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $journals = Journal::where('user_id', auth()->id())
            ->whereMonth('date', Carbon::now()->month)
            ->latest()
            ->get();
            
        return Inertia::render('Mechanic/Journals/Index', [
            'journals' => $journals,
            'todayDate' => Carbon::now()->format('Y-m-d')
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'activity' => 'required|string',
            'date' => 'required|date',
        ]);

        Journal::create([
            'user_id' => auth()->id(),
            'date' => $request->date,
            'activity' => $request->activity,
        ]);

        return back()->with('success', 'Jurnal harian berhasil ditambahkan.');
    }
}
