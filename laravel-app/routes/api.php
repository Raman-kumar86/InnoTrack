<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('dashboard')->name('dashboard.')->group(function (): void {
    Route::get('/sector-distribution', [DashboardController::class, 'sectorDistribution'])
        ->name('sector-distribution');

    Route::get('/state-startup-strength', [DashboardController::class, 'stateStartupStrength'])
        ->name('state-startup-strength');
});