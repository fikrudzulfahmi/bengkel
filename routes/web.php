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
    Route::get('journals', [\App\Http\Controllers\Admin\JournalController::class, 'index'])->name('journals.index');
});

// Mechanic Routes
Route::middleware(['auth', 'role:mekanik'])->prefix('mekanik')->name('mechanic.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'mechanic'])->name('dashboard');

    Route::resource('spareparts', \App\Http\Controllers\Mechanic\SparepartController::class);
    Route::get('transactions/{transaction}/print', [\App\Http\Controllers\Mechanic\TransactionController::class, 'print'])->name('transactions.print');
    Route::resource('transactions', \App\Http\Controllers\Mechanic\TransactionController::class);
    Route::resource('cash-books', \App\Http\Controllers\Mechanic\CashBookController::class);
    Route::resource('journals', \App\Http\Controllers\Mechanic\JournalController::class);
});

require __DIR__.'/auth.php';
