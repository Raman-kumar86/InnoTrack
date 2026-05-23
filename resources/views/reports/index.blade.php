@extends('layouts.app')

@php
    $title = 'Reports';
    $pageTitle = 'Reports';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Reports', 'url' => route('reports.index')],
    ];

    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
    $growthSeries = [120, 144, 156, 169, 188, 205];
    $summary = [
        ['label' => 'Total funding', 'value' => 'Rs 4,820 Cr'],
        ['label' => 'YoY growth', 'value' => '+18.4%'],
        ['label' => 'Approved grants', 'value' => '1,248'],
        ['label' => 'Printable reports', 'value' => '38'],
    ];
@endphp

@section('content')
<section class="space-y-6">
    <x-ui.section-header
        title="Reports"
        subtitle="Export, compare, and print executive-ready summaries for government and startup ecosystem reviews."
    >
        <x-ui.button variant="secondary">
            <x-ui.icon name="download" class="h-4 w-4" />
            Download PDF
        </x-ui.button>
        <x-ui.button>Generate report</x-ui.button>
    </x-ui.section-header>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($summary as $item)
            <x-ui.card>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $item['label'] }}</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900 dark:text-white">{{ $item['value'] }}</p>
            </x-ui.card>
        @endforeach
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <x-ui.card class="xl:col-span-2">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Year-over-year comparison</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Financial and operational growth trends across reporting periods.</p>
                </div>
                <x-ui.badge variant="info">FY comparison</x-ui.badge>
            </div>
            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <x-ui.chart-card title="Funding growth" subtitle="Funding accumulation trend." height="h-64">
                    <canvas data-chart="line" data-labels='@json($months)' data-values='@json($growthSeries)'></canvas>
                </x-ui.chart-card>
                <x-ui.chart-card title="Grant approval rate" subtitle="Reportable approval momentum." height="h-64">
                    <canvas data-chart="bar" data-labels='@json($months)' data-values='@json([20, 24, 28, 33, 35, 38])'></canvas>
                </x-ui.chart-card>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Export center</h3>
            <div class="mt-5 space-y-3">
                <div class="rounded-3xl border border-slate-200 p-4 dark:border-slate-800">
                    <p class="font-medium text-slate-900 dark:text-white">Executive summary</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">PDF, print-ready</p>
                </div>
                <div class="rounded-3xl border border-slate-200 p-4 dark:border-slate-800">
                    <p class="font-medium text-slate-900 dark:text-white">Funding tracker</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">CSV, XLSX</p>
                </div>
                <div class="rounded-3xl border border-slate-200 p-4 dark:border-slate-800">
                    <p class="font-medium text-slate-900 dark:text-white">State analytics pack</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">PDF, chart bundle</p>
                </div>
            </div>
        </x-ui.card>
    </div>

    <x-ui.card>
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Printable report layout</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">A compact preview for board packs, ministry notes, and executive briefings.</p>
            </div>
            <x-ui.button variant="secondary">Print layout</x-ui.button>
        </div>

        <div class="mt-6 rounded-[2rem] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-950">
            <div class="flex flex-col gap-5 border-b border-slate-200 pb-5 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-600 dark:text-indigo-400">Government summary</p>
                    <h4 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Startup India Progress Overview</h4>
                </div>
                <div class="rounded-2xl bg-slate-100 px-4 py-3 text-sm text-slate-600 dark:bg-slate-900 dark:text-slate-300">Prepared for FY 2025-26</div>
            </div>
            <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-3xl bg-slate-50 p-4 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Startup registrations</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">18,420</p>
                </div>
                <div class="rounded-3xl bg-slate-50 p-4 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Funding raised</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Rs 4,820 Cr</p>
                </div>
                <div class="rounded-3xl bg-slate-50 p-4 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Jobs created</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">2.41 L</p>
                </div>
                <div class="rounded-3xl bg-slate-50 p-4 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">States covered</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">28</p>
                </div>
            </div>
        </div>
    </x-ui.card>
</section>
@endsection
