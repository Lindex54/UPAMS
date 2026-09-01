<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserStatusController;
use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('home');
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::middleware(EnsureUserIsActive::class)->group(function (): void {
        Route::patch('/users/{user}/status', UserStatusController::class)->name('users.status.update');
        Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update']);

        $assetManagementModules = [
            'assets',
            'land',
            'buildings',
            'laboratories',
            'vehicles',
            'commercial-property',
            'agricultural-property',
        ];

        foreach ($assetManagementModules as $assetManagementModule) {
            Route::view("/{$assetManagementModule}", 'asset-management.page', [
                'module' => $assetManagementModule,
                'page' => 'index',
            ])->name("asset-management.{$assetManagementModule}.index");

            Route::view("/{$assetManagementModule}/create", 'asset-management.page', [
                'module' => $assetManagementModule,
                'page' => 'create',
            ])->name("asset-management.{$assetManagementModule}.create");

            Route::view("/{$assetManagementModule}/{record}/edit", 'asset-management.page', [
                'module' => $assetManagementModule,
                'page' => 'edit',
            ])->name("asset-management.{$assetManagementModule}.edit");

            Route::view("/{$assetManagementModule}/{record}", 'asset-management.page', [
                'module' => $assetManagementModule,
                'page' => 'show',
            ])->name("asset-management.{$assetManagementModule}.show");
        }

        Route::view('/dashboard', 'dashboard')->name('dashboard');
        Route::view('/estates/dashboard', 'estates.dashboard')->name('estates.dashboard');
        Route::view('/management/dashboard', 'management.dashboard')->name('management.dashboard');
        Route::view('/campus/dashboard', 'campus.dashboard')->name('campus.dashboard');
        Route::view('/finance/dashboard', 'finance.dashboard')->name('finance.dashboard');
    });
});
