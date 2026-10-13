<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));

        $journals = Journal::with('user')
            ->whereDate('date', $date)
            ->latest()
            ->get();

        $activityLogs = ActivityLog::with('user')
            ->whereDate('created_at', $date)
            ->latest()
            ->get();

        return Inertia::render('Admin/Journals/Index', [
            'journals' => $journals,
            'activityLogs' => $activityLogs,
            'filters' => [
                'date' => $date
            ]
        ]);
    }
}
