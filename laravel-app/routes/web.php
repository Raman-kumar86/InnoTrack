<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'dashboard')->name('dashboard');
Route::view('/startups', 'startups.index')->name('startups.index');
Route::view('/startups/create', 'startups.create')->name('startups.create');
Route::view('/startups/{startup}', 'startups.show')->name('startups.show');
Route::view('/startups/{startup}/edit', 'startups.edit')->name('startups.edit');
Route::view('/funding/create', 'funding.create')->name('funding.create');
Route::view('/analytics/state', 'analytics.state')->name('analytics.state');
Route::view('/reports', 'reports.index')->name('reports.index');
Route::view('/users', 'users.index')->name('users.index');
Route::view('/activity-logs', 'activity.index')->name('activity.index');
Route::view('/settings', 'settings.index')->name('settings.index');

Route::prefix('auth')->name('auth.')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::view('/register', 'auth.register')->name('register');
    Route::view('/forgot-password', 'auth.forgot-password')->name('forgot-password');
    Route::view('/reset-password', 'auth.reset-password')->name('reset-password');
});
