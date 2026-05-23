@extends('layouts.app')

@php
    $title = 'Edit Startup';
    $pageTitle = 'Edit Startup';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Startups', 'url' => route('startups.index')],
        ['label' => 'Edit Startup', 'url' => route('startups.edit', ['startup' => 1])],
    ];
@endphp

@section('content')
<section class="space-y-6">
    <x-ui.section-header
        title="Edit startup"
        subtitle="Update metadata, recognition status, and public profile details with a premium admin form layout."
    >
        <x-ui.button href="{{ route('startups.show', ['startup' => 1]) }}" variant="secondary">Preview profile</x-ui.button>
        <x-ui.button type="submit" form="startup-edit-form">Save changes</x-ui.button>
    </x-ui.section-header>

    <form id="startup-edit-form" class="space-y-6">
        <x-ui.card>
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                <x-ui.form-field label="Startup name">
                    <input type="text" class="input-modern" value="AeroNex Robotics" />
                </x-ui.form-field>
                <x-ui.form-field label="Official website">
                    <input type="url" class="input-modern" value="https://aeronex.example" />
                </x-ui.form-field>
                <x-ui.form-field label="Registration number">
                    <input type="text" class="input-modern" value="U72200KA2022PTC123456" />
                </x-ui.form-field>
                <x-ui.select-field label="Sector">
                    <select class="select-modern">
                        <option>Deep Tech</option>
                        <option>SaaS</option>
                        <option>HealthTech</option>
                        <option>FinTech</option>
                    </select>
                </x-ui.select-field>
                <x-ui.select-field label="State">
                    <select class="select-modern">
                        <option>Karnataka</option>
                        <option>Maharashtra</option>
                        <option>Delhi</option>
                        <option>Gujarat</option>
                    </select>
                </x-ui.select-field>
                <x-ui.select-field label="Stage">
                    <select class="select-modern">
                        <option>Pre-Seed</option>
                        <option>Seed</option>
                        <option>Series A</option>
                        <option>Series B</option>
                    </select>
                </x-ui.select-field>
            </div>
        </x-ui.card>

        <x-ui.card>
            <div class="grid gap-5 lg:grid-cols-2">
                <x-ui.form-field label="Primary founder">
                    <input type="text" class="input-modern" value="Ananya Rao" />
                </x-ui.form-field>
                <x-ui.form-field label="Founder email">
                    <input type="email" class="input-modern" value="founder@aeronex.in" />
                </x-ui.form-field>
                <x-ui.form-field label="Short description" class="lg:col-span-2">
                    <textarea class="textarea-modern">Autonomous inspection robotics for manufacturing, logistics, and public infrastructure monitoring.</textarea>
                </x-ui.form-field>
                <x-ui.form-field label="Status">
                    <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-800 dark:bg-slate-900">
                        <div>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">Public listing enabled</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Visible to ministry and state teams</p>
                        </div>
                        <input type="checkbox" class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" checked>
                    </div>
                </x-ui.form-field>
                <x-ui.form-field label="Recognition status">
                    <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-800 dark:bg-slate-900">
                        <div>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">DPIIT recognised</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Eligibility verified against registry</p>
                        </div>
                        <input type="checkbox" class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" checked>
                    </div>
                </x-ui.form-field>
            </div>
        </x-ui.card>

        <div class="sticky bottom-4 z-20 flex justify-end">
            <div class="surface-card flex items-center gap-3 px-4 py-3 shadow-2xl shadow-slate-950/10">
                <x-ui.button href="{{ route('startups.show', ['startup' => 1]) }}" variant="secondary">Cancel</x-ui.button>
                <x-ui.button type="submit">Update startup</x-ui.button>
            </div>
        </div>
    </form>
</section>
@endsection
