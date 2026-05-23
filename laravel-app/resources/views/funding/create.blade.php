@extends('layouts.app')

@php
    $title = 'Add Funding Round';
    $pageTitle = 'Add Funding Round';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Startups', 'url' => route('startups.index')],
        ['label' => 'Funding Round', 'url' => route('funding.create')],
    ];
@endphp

@section('content')
<section class="space-y-6">
    <x-ui.section-header
        title="Add funding round"
        subtitle="Record a new equity, grant, or debt round with a structured approval-ready form layout."
    >
        <x-ui.button href="{{ route('startups.index') }}" variant="secondary">Cancel</x-ui.button>
        <x-ui.button type="submit" form="funding-form">Save round</x-ui.button>
    </x-ui.section-header>

    <form id="funding-form" class="space-y-6">
        <x-ui.card>
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                <x-ui.form-field label="Startup">
                    <input type="text" class="input-modern" value="AeroNex Robotics" />
                </x-ui.form-field>
                <x-ui.select-field label="Round type">
                    <select class="select-modern">
                        <option>Seed</option>
                        <option>Grant</option>
                        <option>Series A</option>
                        <option>Series B</option>
                        <option>Debt</option>
                    </select>
                </x-ui.select-field>
                <x-ui.form-field label="Announced date">
                    <input type="date" class="input-modern" />
                </x-ui.form-field>
                <x-ui.form-field label="Amount">
                    <input type="text" class="input-modern" placeholder="Rs 18 Cr" />
                </x-ui.form-field>
                <x-ui.form-field label="Lead investor">
                    <input type="text" class="input-modern" placeholder="National Growth Fund" />
                </x-ui.form-field>
                <x-ui.form-field label="Valuation">
                    <input type="text" class="input-modern" placeholder="Rs 120 Cr" />
                </x-ui.form-field>
                <x-ui.form-field label="Closing date">
                    <input type="date" class="input-modern" />
                </x-ui.form-field>
                <x-ui.form-field label="Reference number">
                    <input type="text" class="input-modern" placeholder="FND-2026-0012" />
                </x-ui.form-field>
                <x-ui.form-field label="Upload document">
                    <div class="flex cursor-pointer flex-col items-center justify-center rounded-3xl border-2 border-dashed border-slate-200 bg-slate-50 px-6 py-10 text-center dark:border-slate-700 dark:bg-slate-900/60">
                        <x-ui.icon name="download" class="h-8 w-8 text-slate-400" />
                        <p class="mt-3 text-sm font-medium text-slate-700 dark:text-slate-200">Drop term sheet or browse files</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">PDF, DOCX, XLSX up to 10 MB</p>
                    </div>
                </x-ui.form-field>
            </div>
        </x-ui.card>

        <x-ui.card>
            <div class="grid gap-5 lg:grid-cols-2">
                <x-ui.form-field label="Investor notes" class="lg:col-span-2">
                    <textarea class="textarea-modern" placeholder="Add diligence notes, approval references, and milestone conditions."></textarea>
                </x-ui.form-field>
                <x-ui.form-field label="Public disclosure">
                    <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-800 dark:bg-slate-900">
                        <div>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">Show round publicly</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Visible on startup detail page</p>
                        </div>
                        <input type="checkbox" class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" checked>
                    </div>
                </x-ui.form-field>
                <x-ui.form-field label="Government support linked">
                    <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-800 dark:bg-slate-900">
                        <div>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">Linked to incentive scheme</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Connect to a state or central program</p>
                        </div>
                        <input type="checkbox" class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    </div>
                </x-ui.form-field>
            </div>
        </x-ui.card>

        <div class="sticky bottom-4 z-20 flex justify-end">
            <div class="surface-card flex items-center gap-3 px-4 py-3 shadow-2xl shadow-slate-950/10">
                <x-ui.button variant="secondary" type="button">Discard</x-ui.button>
                <x-ui.button type="submit">Save round</x-ui.button>
            </div>
        </div>
    </form>
</section>
@endsection
