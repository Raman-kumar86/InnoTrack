<?php

use App\Http\Controllers\StartupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FundingRoundController;
use App\Http\Controllers\ActivityLogController;
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
Route::get('/startups/search', [FundingRoundController::class, 'searchStartups'])->name('api.startups.search');
Route::middleware('web')->post('/activity-logs/bulk-delete', [ActivityLogController::class, 'bulkDestroy'])->name('activity-logs.bulk-destroy');