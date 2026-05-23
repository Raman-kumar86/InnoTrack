@extends('layouts.app')

@php
    $title = 'Settings';
    $pageTitle = 'Settings';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Settings', 'url' => route('settings.index')],
    ];
@endphp

@section('content')
@php
    $user = Auth::user();
    $displayName = $user->name ?: 'Guest User';
    $roleLabel = ucfirst(str_replace('_', ' ', (string) ($user->role ?? 'viewer')));
    $stateLabel = $user->state ?: '—';
    $profileEmail = $user->email ?: '—';
    $emailAlertsEnabled = true;
    $smsFallbackEnabled = false;
    $sessionTimeoutLabel = '24 hours';
    $sessionTimeoutEnabled = true;
    $activityExportEnabled = true;
@endphp

<section class="space-y-6">
    <x-ui.section-header
        title="Settings"
        subtitle="Manage profile information, password updates, notification preferences, and platform theme settings."
    >
        <x-ui.button variant="secondary" href="{{ route('profile.show') }}">Cancel</x-ui.button>
        <x-ui.button type="submit" form="settings-form">Save settings</x-ui.button>
    </x-ui.section-header>

    <form id="settings-form" action="{{ route('settings.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PATCH')

        <div class="grid gap-6 xl:grid-cols-2">
            <x-ui.card>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Profile settings</h3>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <x-ui.form-field name="name" label="Full name" value="{{ old('name', $displayName) }}" />
                    <x-ui.form-field label="Role" value="{{ $roleLabel }}" readonly />
                    <x-ui.form-field name="email" label="Email" type="email" value="{{ old('email', $profileEmail) }}" />
                    <x-ui.form-field name="state" label="State" value="{{ old('state', $stateLabel === '—' ? '' : $stateLabel) }}" />
                </div>
            </x-ui.card>

            <x-ui.card>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Password update</h3>
                <div class="mt-5 space-y-4">
                    <x-ui.form-field name="current_password" label="Current password" type="password" />
                    <x-ui.form-field name="password" label="New password" type="password" />
                    <x-ui.form-field name="password_confirmation" label="Confirm new password" type="password" />
                </div>
            </x-ui.card>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <x-ui.card>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Notification preferences</h3>
                <div class="mt-5 space-y-4">
                    <label class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-3 dark:border-slate-800">
                        <span>
                            <span class="block text-sm font-medium text-slate-900 dark:text-white">Email alerts</span>
                            <span class="block text-xs text-slate-500 dark:text-slate-400">Daily summary and exception alerts</span>
                        </span>
                        <span class="inline-flex items-center gap-2">
                            <span data-setting-status="email-alerts" class="inline-flex items-center gap-2 rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-500">Enabled</span>
                            <input type="checkbox" data-setting-toggle data-setting-key="email-alerts" class="h-5 w-10 rounded-full border-slate-300 text-indigo-600 focus:ring-indigo-500" checked>
                        </span>
                    </label>
                    <label class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-3 dark:border-slate-800">
                        <span>
                            <span class="block text-sm font-medium {{ $smsFallbackEnabled ? 'text-slate-900 dark:text-white' : 'text-slate-400 line-through' }}">SMS fallback</span>
                            <span class="block text-xs text-slate-500 dark:text-slate-400">Critical approval notifications only</span>
                        </span>
                        <span class="inline-flex items-center gap-2">
                            <span data-setting-status="sms-fallback" class="inline-flex items-center gap-2 rounded-full {{ $smsFallbackEnabled ? 'bg-emerald-500/15 text-emerald-500' : 'bg-slate-200 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }} px-3 py-1 text-xs font-semibold">{{ $smsFallbackEnabled ? 'Enabled' : 'Disabled' }}</span>
                            <input type="checkbox" data-setting-toggle data-setting-key="sms-fallback" class="h-5 w-10 rounded-full border-slate-300 text-indigo-600 focus:ring-indigo-500" {{ $smsFallbackEnabled ? 'checked' : '' }}>
                        </span>
                    </label>
                </div>
            </x-ui.card>

            <x-ui.card>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Theme settings</h3>
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
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-medium text-slate-900 dark:text-white">Two-factor authentication</p>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Enable secure access for admin users.</p>
                            </div>
                            <span class="inline-flex items-center gap-2">
                                <span data-setting-status="two-factor" class="inline-flex items-center gap-2 rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-400">Disabled</span>
                                <input type="checkbox" data-setting-toggle data-setting-key="two-factor" class="h-5 w-10 rounded-full border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            </span>
                        </div>
                    </div>
                    <div class="rounded-3xl border border-slate-200 p-4 dark:border-slate-800">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-medium text-slate-900 dark:text-white">Session timeout</p>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Automatic logout after inactivity. Fixed to {{ $sessionTimeoutLabel }}.</p>
                            </div>
                            <span class="inline-flex items-center gap-2">
                                <span data-setting-status="session-timeout" class="inline-flex items-center gap-2 rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-500">Enabled</span>
                                <input type="checkbox" data-setting-toggle data-setting-key="session-timeout" class="h-5 w-10 rounded-full border-slate-300 text-indigo-600 focus:ring-indigo-500" {{ $sessionTimeoutEnabled ? 'checked' : '' }}>
                            </span>
                        </div>
                    </div>
                    <div class="rounded-3xl border border-slate-200 p-4 dark:border-slate-800">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-medium text-slate-900 dark:text-white">Activity export</p>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Download a security log bundle on demand.</p>
                            </div>
                            <span class="inline-flex items-center gap-2">
                                <span data-setting-status="activity-export" class="inline-flex items-center gap-2 rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-500">Enabled</span>
                                <input type="checkbox" data-setting-toggle data-setting-key="activity-export" class="h-5 w-10 rounded-full border-slate-300 text-indigo-600 focus:ring-indigo-500" {{ $activityExportEnabled ? 'checked' : '' }}>
                            </span>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </form>
</section>
@endsection
