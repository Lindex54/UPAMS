<?php

use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BeneficiaryController;
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

        $operationsModules = [
            'agreements',
            'inspections',
            'maintenance',
            'documents',
        ];

        Route::view('/beneficiaries', 'operations.page', [
            'module' => 'beneficiaries',
            'page' => 'index',
        ])->name('operations.beneficiaries.index');
        Route::get('/beneficiaries/create', [BeneficiaryController::class, 'create'])->name('operations.beneficiaries.create');
        Route::post('/beneficiaries', [BeneficiaryController::class, 'store'])->name('operations.beneficiaries.store');
        Route::get('/beneficiaries/{record}/edit', [BeneficiaryController::class, 'edit'])->name('operations.beneficiaries.edit');
        Route::put('/beneficiaries/{beneficiary}', [BeneficiaryController::class, 'update'])->name('operations.beneficiaries.update');
        Route::view('/beneficiaries/{record}', 'operations.page', [
            'module' => 'beneficiaries',
            'page' => 'show',
        ])->name('operations.beneficiaries.show');

        Route::prefix('api/locations')->name('api.locations.')->group(function (): void {
            Route::get('/districts/{district}/counties', [LocationController::class, 'counties'])->name('counties');
            Route::get('/counties/{county}/sub-counties', [LocationController::class, 'subCounties'])->name('sub-counties');
            Route::get('/sub-counties/{subCounty}/parishes', [LocationController::class, 'parishes'])->name('parishes');
            Route::get('/parishes/{parish}/villages', [LocationController::class, 'villages'])->name('villages');
        });

        foreach ($operationsModules as $operationsModule) {
            Route::view("/{$operationsModule}", 'operations.page', [
                'module' => $operationsModule,
                'page' => 'index',
            ])->name("operations.{$operationsModule}.index");

            Route::view("/{$operationsModule}/create", 'operations.page', [
                'module' => $operationsModule,
                'page' => 'create',
            ])->name("operations.{$operationsModule}.create");

            Route::view("/{$operationsModule}/{record}/edit", 'operations.page', [
                'module' => $operationsModule,
                'page' => 'edit',
            ])->name("operations.{$operationsModule}.edit");

            Route::view("/{$operationsModule}/{record}", 'operations.page', [
                'module' => $operationsModule,
                'page' => 'show',
            ])->name("operations.{$operationsModule}.show");
        }

        Route::view('/agreements/{record}/approval', 'operations.page', [
            'module' => 'agreements',
            'page' => 'approval',
        ])->name('operations.agreements.approval');

        Route::view('/agreements/{record}/renewal', 'operations.page', [
            'module' => 'agreements',
            'page' => 'renewal',
        ])->name('operations.agreements.renewal');

        Route::view('/agreements/{record}/termination', 'operations.page', [
            'module' => 'agreements',
            'page' => 'termination',
        ])->name('operations.agreements.termination');

        Route::view('/dashboard', 'dashboard')->name('dashboard');
        Route::view('/estates/dashboard', 'estates.dashboard')->name('estates.dashboard');
        Route::view('/management/dashboard', 'management.dashboard')->name('management.dashboard');
        Route::view('/campus/dashboard', 'campus.dashboard')->name('campus.dashboard');
        Route::view('/finance/dashboard', 'finance.dashboard')->name('finance.dashboard');
    });
});
