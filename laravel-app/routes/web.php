<?php

use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
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
    Route::view('/startups', 'startups.index')->name('startups.index');
    Route::view('/startups/create', 'startups.create')->name('startups.create');
    Route::view('/startups/{startup}', 'startups.show')->name('startups.show');
    Route::view('/startups/{startup}/edit', 'startups.edit')->name('startups.edit');
    Route::view('/funding/create', 'funding.create')->name('funding.create');
    Route::view('/analytics/state', 'analytics.state')->name('analytics.state');
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::view('/users', 'users.index')->name('users.index');
    Route::view('/activity-logs', 'activity.index')->name('activity.index');
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.index');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('/setting', [SettingsController::class, 'edit'])->name('setting.alias');
    Route::view('/profile', 'profile.show')->name('profile.show');
});
