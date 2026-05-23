@extends('layouts.app')

@php
    $title = 'Add Startup';
    $pageTitle = 'Add Startup';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Startups', 'url' => route('startups.index')],
        ['label' => 'Add Startup', 'url' => route('startups.create')],
    ];
@endphp

@section('content')
<section class="space-y-6">
    <x-ui.section-header
        title="Add startup"
        subtitle="Capture a new startup record using a multi-column responsive form with validation-ready fields."
    >
        <x-ui.button href="{{ route('startups.index') }}" variant="secondary">Cancel</x-ui.button>
        <x-ui.button type="submit" form="startup-form">Save startup</x-ui.button>
    </x-ui.section-header>

    <form id="startup-form" class="space-y-6">
        <x-ui.card>
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                <x-ui.form-field label="Startup name">
                    <input type="text" class="input-modern" placeholder="AeroNex Robotics" />
                </x-ui.form-field>
                <x-ui.form-field label="Official website">
                    <input type="url" class="input-modern" placeholder="https://startup.example" />
                </x-ui.form-field>
                <x-ui.form-field label="Registration number">
                    <input type="text" class="input-modern" placeholder="U72200KA2022PTC123456" />
                </x-ui.form-field>
                <x-ui.select-field label="Sector">
                    <select class="select-modern">
                        <option>Deep Tech</option>
                        <option>SaaS</option>
                        <option>HealthTech</option>
                        <option>FinTech</option>
                        <option>Agritech</option>
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
                <x-ui.form-field label="Founded date">
                    <input type="date" class="input-modern" />
                </x-ui.form-field>
                <x-ui.form-field label="Founder count">
                    <input type="number" class="input-modern" placeholder="2" />
                </x-ui.form-field>
                <x-ui.form-field label="Employee count">
                    <input type="number" class="input-modern" placeholder="84" />
                </x-ui.form-field>
            </div>
        </x-ui.card>

        <x-ui.card>
            <div class="grid gap-5 lg:grid-cols-2">
                <x-ui.form-field label="Primary founder">
                    <input type="text" class="input-modern" placeholder="Ananya Rao" />
                </x-ui.form-field>
                <x-ui.form-field label="Founder email">
                    <input type="email" class="input-modern" placeholder="founder@startup.in" />
                </x-ui.form-field>
                <x-ui.form-field label="Short description" class="lg:col-span-2">
                    <textarea class="textarea-modern" placeholder="Describe the startup mission and sector focus."></textarea>
                </x-ui.form-field>
                <x-ui.form-field label="Logo upload">
                    <div class="flex cursor-pointer flex-col items-center justify-center rounded-3xl border-2 border-dashed border-slate-200 bg-slate-50 px-6 py-10 text-center dark:border-slate-700 dark:bg-slate-900/60">
                        <x-ui.icon name="download" class="h-8 w-8 text-slate-400" />
                        <p class="mt-3 text-sm font-medium text-slate-700 dark:text-slate-200">Drop logo or browse files</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">SVG, PNG, JPG up to 5 MB</p>
                    </div>
                </x-ui.form-field>
                <x-ui.form-field label="Recognition status">
                    <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-800 dark:bg-slate-900">
                        <input id="dpitt" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" checked>
                        <label for="dpitt" class="text-sm text-slate-700 dark:text-slate-300">DPIIT recognised</label>
                    </div>
                </x-ui.form-field>
            </div>
        </x-ui.card>

        <div class="sticky bottom-4 z-20 flex justify-end">
            <div class="surface-card flex items-center gap-3 px-4 py-3 shadow-2xl shadow-slate-950/10">
                <x-ui.button variant="secondary" type="button">Discard</x-ui.button>
                <x-ui.button type="submit">Save startup</x-ui.button>
            </div>
        </div>
    </form>
</section>
@endsection
