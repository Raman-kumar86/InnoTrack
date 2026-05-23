@extends('layouts.app')

@php
    $title = 'Profile';
    $pageTitle = 'Profile';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Profile', 'url' => route('profile.show')],
    ];

    $user = Auth::user();
    $displayName = $user->name ?: 'Guest User';
    $roleLabel = ucfirst(str_replace('_', ' ', (string) ($user->role ?? 'viewer')));
    $stateLabel = $user->state ?: '—';
    $profileEmail = $user->email ?: '—';
    $emailAlertsEnabled = true;
    $smsFallbackEnabled = false;
    $sessionTimeoutLabel = '24 hours';
@endphp

@section('content')
<section class="space-y-6">
    <x-ui.section-header
        title="Profile"
        subtitle="Read-only profile view. Use Edit Profile to continue to settings."
    >
        <a href="{{ route('dashboard') }}">
            <x-ui.button variant="secondary">Back</x-ui.button>
        </a>
        <a href="{{ route('setting.alias') }}" class="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-950">
            Edit Profile
        </a>
    </x-ui.section-header>

    <div class="grid gap-6 xl:grid-cols-2">
        <x-ui.card>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Profile settings</h3>
            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <x-ui.form-field label="Full name" value="{{ $displayName }}" readonly />
                <x-ui.form-field label="Role" value="{{ $roleLabel }}" readonly />
                <x-ui.form-field label="Email" value="{{ $profileEmail }}" readonly />
                <x-ui.form-field label="State" value="{{ $stateLabel }}" readonly />
            </div>
        </x-ui.card>

    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <x-ui.card>
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Notification preferences</h3>
                <span data-theme-summary class="inline-flex items-center gap-1 rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">Light</span>
            </div>
            <div class="mt-5 space-y-4">
                <label class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-3 dark:border-slate-800">
                    <span>
                        <span class="block text-sm font-medium text-slate-900 dark:text-white">Email alerts</span>
                        <span class="block text-xs text-slate-500 dark:text-slate-400">Daily summary and exception alerts</span>
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <span data-setting-summary="email-alerts" class="inline-flex items-center gap-2 rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-500">Enabled</span>
                        <input data-setting-display="email-alerts" type="checkbox" class="h-5 w-10 rounded-full border-slate-300 text-indigo-600 focus:ring-indigo-500" checked disabled>
                    </span>
                </label>

                <label class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-3 dark:border-slate-800">
                    <span>
                        <span class="block text-sm font-medium {{ $smsFallbackEnabled ? 'text-slate-900 dark:text-white' : 'text-slate-400 line-through' }}">SMS fallback</span>
                        <span class="block text-xs {{ $smsFallbackEnabled ? 'text-slate-500 dark:text-slate-400' : 'text-slate-400 line-through' }}">Critical approval notifications only</span>
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <span data-setting-summary="sms-fallback" class="inline-flex items-center gap-2 rounded-full {{ $smsFallbackEnabled ? 'bg-emerald-500/15 text-emerald-500' : 'bg-slate-200 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }} px-3 py-1 text-xs font-semibold">{{ $smsFallbackEnabled ? 'Enabled' : 'Disabled' }}</span>
                        <input data-setting-display="sms-fallback" type="checkbox" class="h-5 w-10 rounded-full border-slate-300 text-indigo-600 focus:ring-indigo-500" {{ $smsFallbackEnabled ? 'checked' : '' }} disabled>
                    </span>
                </label>
            </div>
        </x-ui.card>

        <x-ui.card>
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Theme settings</h3>
                <span data-theme-summary class="inline-flex items-center gap-1 rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-500">Dark</span>
            </div>
            <div class="mt-5 space-y-4">
                <div data-theme-card="light" class="rounded-3xl border border-slate-200 p-4 dark:border-slate-800 opacity-70">
                    <p class="text-sm font-medium text-slate-900 dark:text-white">Light mode</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Best for shared office environments.</p>
                </div>
                <div data-theme-card="dark" class="rounded-3xl border border-emerald-400/30 bg-emerald-500/5 p-4 dark:border-emerald-400/30 dark:bg-emerald-500/10">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">Dark mode</p>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Best for analysis-heavy, low-light workflows.</p>
                        </div>
                        <span data-theme-status class="inline-flex items-center gap-1 rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-500">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            <span data-theme-status-label>Active</span>
                        </span>
                    </div>
                </div>
                <div class="flex gap-3">
                    <x-ui.button type="button" variant="secondary" data-theme-option="light" class="flex-1 justify-center">Light</x-ui.button>
                    <x-ui.button type="button" variant="secondary" data-theme-option="dark" class="flex-1 justify-center">Dark</x-ui.button>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Account security</h3>
            <div class="mt-5 space-y-4">
                <div class="rounded-3xl border border-slate-200 p-4 dark:border-slate-800">
                    <p class="text-sm font-medium text-slate-900 dark:text-white">Two-factor authentication</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Enable secure access for admin users.</p>
                </div>
                <div class="rounded-3xl border border-slate-200 p-4 dark:border-slate-800">
                    <p class="text-sm font-medium text-slate-900 dark:text-white">Session timeout</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Automatic logout after inactivity. Fixed to {{ $sessionTimeoutLabel }}.</p>
                    <span class="mt-3 inline-flex items-center gap-2 rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-500">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Enabled
                    </span>
                </div>
                <div class="rounded-3xl border border-slate-200 p-4 dark:border-slate-800">
                    <p class="text-sm font-medium text-slate-900 dark:text-white">Activity export</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Download a security log bundle on demand.</p>
                    <span class="mt-3 inline-flex items-center gap-2 rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-500">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Enabled
                    </span>
                </div>
            </div>
        </x-ui.card>
    </div>
</section>
@endsection
