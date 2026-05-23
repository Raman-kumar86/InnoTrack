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
<section class="space-y-6">
    <x-ui.section-header
        title="Settings"
        subtitle="Manage profile information, password updates, notification preferences, and platform theme settings."
    >
        <x-ui.button variant="secondary">Cancel</x-ui.button>
        <x-ui.button>Save settings</x-ui.button>
    </x-ui.section-header>

    <div class="grid gap-6 xl:grid-cols-2">
        <x-ui.card>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Profile settings</h3>
            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <x-ui.form-field label="Full name"><input type="text" class="input-modern" value="Aarav Sharma"></x-ui.form-field>
                <x-ui.form-field label="Designation"><input type="text" class="input-modern" value="Program Administrator"></x-ui.form-field>
                <x-ui.form-field label="Email"><input type="email" class="input-modern" value="aarav@startupindia.gov.in"></x-ui.form-field>
                <x-ui.form-field label="Phone"><input type="tel" class="input-modern" value="+91 98765 43210"></x-ui.form-field>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Password update</h3>
            <div class="mt-5 space-y-4">
                <x-ui.form-field label="Current password"><input type="password" class="input-modern"></x-ui.form-field>
                <x-ui.form-field label="New password"><input type="password" class="input-modern"></x-ui.form-field>
                <x-ui.form-field label="Confirm new password"><input type="password" class="input-modern"></x-ui.form-field>
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
                    <input type="checkbox" class="h-5 w-10 rounded-full border-slate-300 text-indigo-600 focus:ring-indigo-500" checked>
                </label>
                <label class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-3 dark:border-slate-800">
                    <span>
                        <span class="block text-sm font-medium text-slate-900 dark:text-white">SMS fallback</span>
                        <span class="block text-xs text-slate-500 dark:text-slate-400">Critical approval notifications only</span>
                    </span>
                    <input type="checkbox" class="h-5 w-10 rounded-full border-slate-300 text-indigo-600 focus:ring-indigo-500">
                </label>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Theme settings</h3>
            <div class="mt-5 space-y-4">
                <div class="rounded-3xl border border-slate-200 p-4 dark:border-slate-800">
                    <p class="text-sm font-medium text-slate-900 dark:text-white">Light mode</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Best for shared office environments.</p>
                </div>
                <div class="rounded-3xl border border-slate-200 p-4 dark:border-slate-800">
                    <p class="text-sm font-medium text-slate-900 dark:text-white">Dark mode</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Best for analysis-heavy, low-light workflows.</p>
                </div>
                <div class="flex gap-3">
                    <x-ui.button variant="secondary" class="flex-1 justify-center">Light</x-ui.button>
                    <x-ui.button variant="secondary" class="flex-1 justify-center">Dark</x-ui.button>
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
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Automatic logout after inactivity.</p>
                </div>
                <div class="rounded-3xl border border-slate-200 p-4 dark:border-slate-800">
                    <p class="text-sm font-medium text-slate-900 dark:text-white">Activity export</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Download a security log bundle on demand.</p>
                </div>
            </div>
        </x-ui.card>
    </div>
</section>
@endsection
