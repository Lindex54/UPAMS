<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('home');
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::view('/estates/dashboard', 'estates.dashboard')->name('estates.dashboard');
    Route::view('/management/dashboard', 'management.dashboard')->name('management.dashboard');
    Route::view('/campus/dashboard', 'campus.dashboard')->name('campus.dashboard');
    Route::view('/finance/dashboard', 'finance.dashboard')->name('finance.dashboard');
});
