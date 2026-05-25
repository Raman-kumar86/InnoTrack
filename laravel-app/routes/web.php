<?php

use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\FundingRoundController;
use App\Http\Controllers\StateAnalyticsController;
use App\Http\Controllers\StartupController;
use App\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->name('auth.')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.store');

        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('register.store');

        Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot-password');
        Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink'])->name('forgot-password.send');

        Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('reset-password');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password.update');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/startups', [StartupController::class, 'index'])->name('startups.index');
    Route::get('/startups/export', [StartupController::class, 'export'])->name('startups.export');
    Route::get('/startups/create', [StartupController::class, 'create'])->name('startups.create');
    Route::get('/startups/{startup}', [StartupController::class, 'show'])->name('startups.show');
    Route::get('/startups/{startup}/edit', [StartupController::class, 'edit'])->name('startups.edit');
    Route::patch('/startups/{startup}', [StartupController::class, 'update'])->name('startups.update');
    Route::delete('/startups/{startup}', [StartupController::class, 'destroy'])->name('startups.destroy');
    Route::get('/funding/create', [FundingRoundController::class, 'create'])->name('funding.create');
    Route::get('/startups/{startup}/funding/create', [FundingRoundController::class, 'create'])->name('startups.funding.create');
    Route::post('/funding', [FundingRoundController::class, 'store'])->name('funding.store');
    Route::get('/funding/{fundingRound}/edit', [FundingRoundController::class, 'edit'])->name('funding.edit');
    Route::put('/funding/{fundingRound}', [FundingRoundController::class, 'update'])->name('funding.update');
    Route::delete('/funding/{fundingRound}', [FundingRoundController::class, 'destroy'])->name('funding.destroy');
    Route::get('/state-analytics', [StateAnalyticsController::class, 'index'])->name('state-analytics.index');
    Route::get('/state-analytics/export', [StateAnalyticsController::class, 'export'])->name('state-analytics.export');
    // Backwards-compatible alias used by older views/components
    Route::get('/analytics/state', function () {
        return redirect()->route('state-analytics.index');
    })->name('analytics.state');
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/executive', [ReportsController::class, 'exportExecutive'])->name('reports.export.executive');
    Route::get('/reports/export/funding', [ReportsController::class, 'exportFunding'])->name('reports.export.funding');
    Route::get('/reports/export/states', [ReportsController::class, 'exportStates'])->name('reports.export.states');
    Route::view('/users', 'users.index')->name('users.index');
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');
    Route::delete('/activity-logs/{activityLog}', [ActivityLogController::class, 'destroy'])->name('activity-logs.destroy');
    Route::redirect('/activity', '/activity-logs')->name('activity.index');
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.index');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('/setting', [SettingsController::class, 'edit'])->name('setting.alias');
    Route::view('/profile', 'profile.show')->name('profile.show');
});
