@extends('layouts.app')

@php
// View variables are provided by App\Http\Controllers\ReportsController
@endphp

@section('content')
<section class="space-y-6">
    <x-ui.section-header
        title="Reports"
        subtitle="Export, compare, and print executive-ready summaries for government and startup ecosystem reviews.">
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
                    <x-slot:action>
                        <form method="get" action="{{ route('reports.index') }}" class="flex items-center gap-2">
                            <label class="text-xs text-slate-500">From</label>
                            <input type="date" name="start_date" value="{{ $selectedStartDate ?? '' }}" class="form-input" />
                            <label class="text-xs text-slate-500">To</label>
                            <input type="date" name="end_date" value="{{ $selectedEndDate ?? '' }}" class="form-input" />
                            <x-ui.button type="submit" variant="secondary">Filter</x-ui.button>
                        </form>
                    </x-slot:action>
                    <canvas data-chart="line" data-labels='@json($months)' data-datasets='@json($growthDatasets)'></canvas>
                    <p class="mt-2 text-xs text-slate-500">Showing: {{ $selectedRangeLabel ?? '' }}</p>
                </x-ui.chart-card>

                <x-ui.chart-card title="Grant approval rate" subtitle="Reportable approval momentum." height="h-64">
                    <x-slot:action>
                        <form method="get" action="{{ route('reports.index') }}" class="flex items-center gap-2">
                            <label class="text-xs text-slate-500">From</label>
                            <input type="date" name="start_date" value="{{ $selectedStartDate ?? '' }}" class="form-input" />
                            <label class="text-xs text-slate-500">To</label>
                            <input type="date" name="end_date" value="{{ $selectedEndDate ?? '' }}" class="form-input" />
                            <x-ui.button type="submit" variant="secondary">Filter</x-ui.button>
                        </form>
                    </x-slot:action>
                    <canvas data-chart="bar" data-labels='@json($months)' data-datasets='@json($approvalDatasets)'></canvas>
                    <p class="mt-2 text-xs text-slate-500">Showing: {{ $selectedRangeLabel ?? '' }}</p>
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
</div>

    <x-ui.card class="mt-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Monthly detail</h3>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Per-month funding, registrations and approval rates for the selected range.</p>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm table-auto border-collapse">
                <thead>
                    <tr class="text-left text-xs text-slate-500">
                        <th class="px-3 py-2">Month</th>
                        <th class="px-3 py-2">Funding (Rs.)</th>
                        <th class="px-3 py-2">Cumulative (Rs.)</th>
                        <th class="px-3 py-2">Registrations</th>
                        <th class="px-3 py-2">Recognized</th>
                        <th class="px-3 py-2">Approval %</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tableRows as $row)
                    <tr>
                        <td class="px-3 py-2 text-slate-700">{{ $row['label'] }}</td>
                        <td class="px-3 py-2">{{ number_format($row['funding'], 2) }}</td>
                        <td class="px-3 py-2">{{ number_format($row['cumulative'], 2) }}</td>
                        <td class="px-3 py-2">{{ number_format($row['registrations']) }}</td>
                        <td class="px-3 py-2">{{ number_format($row['recognized']) }}</td>
                        <td class="px-3 py-2">{{ number_format($row['approval'], 1) }}%</td>
                    </tr>
                    @empty
                    <tr>
                        <td class="px-3 py-4 text-slate-500" colspan="6">No data for the selected range.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

</section>
@endsection