<?php

use App\Http\Controllers\StartupController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('dashboard')->name('dashboard.')->group(function (): void {
    Route::get('/sector-distribution', [DashboardController::class, 'sectorDistribution'])
        ->name('sector-distribution');

    Route::get('/state-startup-strength', [DashboardController::class, 'stateStartupStrength'])
        ->name('state-startup-strength');
});

Route::post('/startups/bulk-delete', [StartupController::class, 'bulkDestroy']);
Route::post('/startups/bulk-export', [StartupController::class, 'bulkExport']);
Route::post('/startups/bulk-status', [StartupController::class, 'bulkStatusUpdate']);