<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    if (auth()->user()->role === 'bendahara') {
        return redirect()->route('bendahara.dashboard');
    }
    return redirect()->route('mechanic.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'admin'])->name('dashboard');

    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/print-service', [\App\Http\Controllers\Admin\ReportController::class, 'printService'])->name('reports.print-service');
    Route::get('reports/print-finance', [\App\Http\Controllers\Admin\ReportController::class, 'printFinance'])->name('reports.print-finance');
    Route::get('journals', [\App\Http\Controllers\Admin\JournalController::class, 'index'])->name('journals.index');
});

// Mechanic Routes
Route::middleware(['auth', 'role:mekanik'])->prefix('mekanik')->name('mechanic.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'mechanic'])->name('dashboard');

    Route::resource('spareparts', \App\Http\Controllers\Mechanic\SparepartController::class);
    Route::get('transactions/{transaction}/print', [\App\Http\Controllers\Mechanic\TransactionController::class, 'print'])->name('transactions.print');
    Route::resource('transactions', \App\Http\Controllers\Mechanic\TransactionController::class);
    Route::post('cash-books/setor-bendahara', [\App\Http\Controllers\Mechanic\CashBookController::class, 'setorBendahara'])->name('cash-books.setor-bendahara');
    Route::resource('cash-books', \App\Http\Controllers\Mechanic\CashBookController::class);
    Route::resource('journals', \App\Http\Controllers\Mechanic\JournalController::class);
});

// Bendahara Routes
Route::middleware(['auth', 'role:bendahara'])->prefix('bendahara')->name('bendahara.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'bendahara'])->name('dashboard');

    Route::resource('cash-books', \App\Http\Controllers\Bendahara\CashBookController::class);
    Route::get('deposits', [\App\Http\Controllers\Bendahara\DepositController::class, 'index'])->name('deposits.index');
    Route::post('deposits/{deposit}/approve', [\App\Http\Controllers\Bendahara\DepositController::class, 'approve'])->name('deposits.approve');
    Route::post('deposits/{deposit}/reject', [\App\Http\Controllers\Bendahara\DepositController::class, 'reject'])->name('deposits.reject');
});

require __DIR__.'/auth.php';

