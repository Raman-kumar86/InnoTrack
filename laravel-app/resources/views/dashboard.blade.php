@extends('layouts.app')

@php
$title = $title ?? 'Dashboard';
$pageTitle = $pageTitle ?? 'Startup India Progress Dashboard';
$breadcrumbs = $breadcrumbs ?? [
['label' => 'Home', 'url' => route('dashboard')],
['label' => 'Dashboard', 'url' => route('dashboard')],
];

$sparkLabels = $sparkLabels ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
$totalSpark = $totalSpark ?? [1400, 1600, 1525, 1780, 1900, 2140];
$recognizedSpark = $recognizedSpark ?? [900, 980, 1040, 1080, 1180, 1260];
$fundingSpark = $fundingSpark ?? [320, 360, 390, 420, 460, 520];
$jobsSpark = $jobsSpark ?? [4200, 4450, 4680, 4900, 5150, 5480];

$months = $months ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$registrations = $registrations ?? [112, 128, 140, 165, 178, 190, 210, 222, 235, 248, 260, 279];
$sectors = $sectors ?? [
['label' => 'SaaS', 'value' => 42, 'color' => '#4f46e5'],
['label' => 'HealthTech', 'value' => 18, 'color' => '#0ea5e9'],
['label' => 'FinTech', 'value' => 20, 'color' => '#10b981'],
['label' => 'Agritech', 'value' => 12, 'color' => '#f59e0b'],
['label' => 'Deep Tech', 'value' => 8, 'color' => '#64748b'],
];
$stateLabels = $stateLabels ?? ['Karnataka', 'Maharashtra', 'Delhi', 'Telangana', 'Tamil Nadu', 'Gujarat'];
$stateFunding = $stateFunding ?? [480, 455, 410, 388, 365, 342];
$fundingSeries = $fundingSeries ?? [260, 290, 320, 360, 402, 450, 498, 560, 612, 685, 742, 810];

$activities = $activities ?? [
['title' => 'New DPIIT recognition approved', 'meta' => '42 minutes ago', 'tone' => 'success', 'icon' => 'shield-check'],
['title' => 'Funding round submitted for review', 'meta' => '2 hours ago', 'tone' => 'info', 'icon' => 'funding'],
['title' => 'State analytics refreshed', 'meta' => '4 hours ago', 'tone' => 'neutral', 'icon' => 'map'],
['title' => 'Quarterly report exported', 'meta' => 'Yesterday', 'tone' => 'warning', 'icon' => 'download'],
];

$latestStartups = $latestStartups ?? [
['name' => 'AeroNex Robotics', 'sector' => 'Deep Tech', 'state' => 'Karnataka', 'stage' => 'Series A', 'status' => 'Active', 'dpiit' => 'Yes', 'date' => '12 Apr 2026'],
['name' => 'SwasthGrid', 'sector' => 'HealthTech', 'state' => 'Maharashtra', 'stage' => 'Seed', 'status' => 'Under Review', 'dpiit' => 'No', 'date' => '10 Apr 2026'],
['name' => 'CropPulse', 'sector' => 'Agritech', 'state' => 'Gujarat', 'stage' => 'Pre-Seed', 'status' => 'Active', 'dpiit' => 'Yes', 'date' => '08 Apr 2026'],
['name' => 'LedgerLoop', 'sector' => 'FinTech', 'state' => 'Delhi', 'stage' => 'Series B', 'status' => 'Active', 'dpiit' => 'Yes', 'date' => '06 Apr 2026'],
];

$topStates = $topStates ?? [
['name' => 'Karnataka', 'startups' => 4812, 'growth' => '+12.5%'],
['name' => 'Maharashtra', 'startups' => 4308, 'growth' => '+10.2%'],
['name' => 'Delhi', 'startups' => 2890, 'growth' => '+9.7%'],
['name' => 'Telangana', 'startups' => 2661, 'growth' => '+8.9%'],
];

$heatmapRows = $heatmapRows ?? [
['state' => 'Karnataka', 'cells' => [18, 26, 32, 42, 50, 60]],
['state' => 'Maharashtra', 'cells' => [16, 22, 29, 35, 46, 55]],
['state' => 'Delhi', 'cells' => [12, 18, 24, 30, 38, 44]],
['state' => 'Telangana', 'cells' => [10, 14, 20, 26, 32, 40]],
];

$sectorCards = $sectorCards ?? [
['name' => 'SaaS', 'value' => '42%', 'subtitle' => 'Largest share of DPIIT recognitions'],
['name' => 'HealthTech', 'value' => '18%', 'subtitle' => 'Strong hospital and public health adoption'],
['name' => 'FinTech', 'value' => '20%', 'subtitle' => 'Highest funding velocity in metro states'],
];

// prepare datasets for charts to avoid Blade/parser issues with parentheses inside strings
$registrationDatasets = $registrationDatasets ?? [[
'label' => 'Registrations',
'data' => $registrations,
'borderColor' => '#4f46e5',
'backgroundColor' => 'rgba(79, 70, 229, 0.12)',
'tension' => 0.42,
'fill' => true,
]];

$sectorLabels = $sectorLabels ?? array_column($sectors, 'label');
$sectorValues = $sectorValues ?? array_column($sectors, 'value');
$sectorColors = $sectorColors ?? array_column($sectors, 'color');
$sectorDatasets = $sectorDatasets ?? [[
'data' => $sectorValues,
'backgroundColor' => $sectorColors,
]];
$sectorModalRows = $sectorModalRows ?? [];
$pieSectorRows = $pieSectorRows ?? [];
$sectorTotalActive = $sectorTotalActive ?? 0;

@endphp

@section('content')
<section class="space-y-6">
    <x-ui.section-header
        title="Startup India Progress Dashboard"
        subtitle="Government-grade analytics for startup recognition, funding flows, jobs created, and state performance across India.">
        <x-ui.button href="{{ route('reports.index') }}" variant="secondary">
            <x-ui.icon name="download" class="h-4 w-4" />
            Export report
        </x-ui.button>
        <x-ui.button href="{{ route('startups.create') }}">
            <x-ui.icon name="plus" class="h-4 w-4" />
            Add startup
        </x-ui.button>
    </x-ui.section-header>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($kpiCards as $card)
        <x-ui.kpi-card
            :label="$card['label']"
            :value="$card['value']"
            :trend="$card['trend'] ?? null"
            :icon="$card['icon'] ?? null"
            :description="$card['description'] ?? null" />
        @endforeach
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <x-ui.chart-card title="Monthly startup registrations" subtitle="New registrations and profile updates over the last twelve months.">
            <canvas data-chart="line" data-labels='@json($months)' data-datasets='@json($registrationDatasets)'></canvas>
        </x-ui.chart-card>

        <div id="sector-distribution-widget" class="xl:col-span-1" data-api-url="{{ route('dashboard.sector-distribution') }}" data-title="Sector distribution" data-subtitle="Share of active startups by dominant sector."></div>

        <x-ui.chart-card title="State-wise startup strength" subtitle="Top startup states ranked by active ecosystem volume.">
            <canvas data-chart="bar" data-horizontal="true" data-labels='@json($stateLabels)' data-values='@json($stateFunding)'></canvas>
        </x-ui.chart-card>

        <x-ui.chart-card title="Funding growth" subtitle="Cumulative funding growth across the fiscal year.">
            <canvas data-chart="line" data-labels='@json($months)' data-values='@json($fundingSeries)'></canvas>
        </x-ui.chart-card>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <x-ui.card class="xl:col-span-2">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Latest registered startups</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Recent onboarding activity from the national startup registry.</p>
                </div>

    @push('scripts')
        @vite('resources/js/sector-distribution-widget.jsx')
    @endpush
                <x-ui.button href="{{ route('startups.index') }}" variant="secondary">View all</x-ui.button>
            </div>

            <div class="mt-6 overflow-hidden">
                <x-ui.table>
                    <thead class="table-head">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Startup</th>
                            <th class="px-6 py-4 font-semibold">Sector</th>
                            <th class="px-6 py-4 font-semibold">State</th>
                            <th class="px-6 py-4 font-semibold">Stage</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold">DPIIT</th>
                            <th class="px-6 py-4 font-semibold">Founded</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach ($latestStartups as $startup)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                            <td class="table-cell font-medium text-slate-900 dark:text-white">{{ $startup['name'] }}</td>
                            <td class="table-cell">{{ $startup['sector'] }}</td>
                            <td class="table-cell">{{ $startup['state'] }}</td>
                            <td class="table-cell">{{ $startup['stage'] }}</td>
                            <td class="table-cell">
                                <x-ui.badge :variant="$startup['status'] === 'Active' ? 'success' : 'warning'">{{ $startup['status'] }}</x-ui.badge>
                            </td>
                            <td class="table-cell">{{ $startup['dpiit'] }}</td>
                            <td class="table-cell">{{ $startup['date'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>
            </div>
        </x-ui.card>

        <x-ui.card>
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Recent activity</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Audit-friendly feed for operational monitoring.</p>
                </div>
            </div>

            <div class="mt-6 space-y-4">
                @foreach ($activities as $activity)
                <div class="flex gap-4 rounded-3xl border border-slate-200 p-4 transition hover:border-indigo-200 hover:bg-slate-50 dark:border-slate-800 dark:hover:border-indigo-500/30 dark:hover:bg-slate-900/80">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-indigo-600/10 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                        <x-ui.icon name="{{ $activity['icon'] }}" class="h-5 w-5" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-medium text-slate-900 dark:text-white">{{ $activity['title'] }}</p>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $activity['meta'] }}</p>
                    </div>
                    <span class="self-start rounded-full px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] {{ $activity['tone'] === 'success' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : ($activity['tone'] === 'warning' ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300') }}">{{ $activity['tone'] }}</span>
                </div>
                @endforeach
            </div>
        </x-ui.card>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <x-ui.card class="xl:col-span-2">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Funding heatmap</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Relative funding intensity across major states and quarters.</p>
                </div>
                <x-ui.badge variant="info">FY 2025-26</x-ui.badge>
            </div>

            <div class="mt-6 space-y-3">
                <div class="grid grid-cols-[140px_repeat(6,minmax(0,1fr))] gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                    <span></span>
                    <span>Q1</span><span>Q2</span><span>Q3</span><span>Q4</span><span>Q5</span><span>Q6</span>
                </div>
                @foreach ($heatmapRows as $row)
                <div class="grid grid-cols-[140px_repeat(6,minmax(0,1fr))] gap-2">
                    <div class="flex items-center rounded-2xl bg-slate-50 px-3 py-3 text-sm font-medium text-slate-700 dark:bg-slate-900 dark:text-slate-200">{{ $row['state'] }}</div>
                    @foreach ($row['cells'] as $cell)
                    @php
                    $heatmapClass = $cell >= 50
                    ? 'bg-indigo-700'
                    : ($cell >= 40
                    ? 'bg-indigo-600'
                    : ($cell >= 30
                    ? 'bg-indigo-500'
                    : ($cell >= 20
                    ? 'bg-indigo-400'
                    : 'bg-indigo-300')));
                    @endphp
                    <div class="rounded-2xl px-3 py-3 text-center text-sm font-semibold text-white {{ $heatmapClass }}">{{ $cell }}</div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </x-ui.card>

        <div class="space-y-6">
            <x-ui.card>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Top performing states</h3>
                <div class="mt-5 space-y-4">
                    @foreach ($topStates as $state)
                    <div class="rounded-3xl border border-slate-200 p-4 dark:border-slate-800">
                        <div class="flex items-center justify-between gap-3">
                            <p class="font-medium text-slate-900 dark:text-white">{{ $state['name'] }}</p>
                            <span class="rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400">{{ $state['growth'] }}</span>
                        </div>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ number_format($state['startups']) }} active startups</p>
                    </div>
                    @endforeach
                </div>
            </x-ui.card>

            <x-ui.card>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Sector performance</h3>
                <div class="mt-5 space-y-4">
                    @foreach ($sectorCards as $sector)
                    <div class="rounded-3xl border border-slate-200 p-4 dark:border-slate-800">
                        <div class="flex items-center justify-between gap-3">
                            <p class="font-medium text-slate-900 dark:text-white">{{ $sector['name'] }}</p>
                            <span class="text-2xl font-semibold text-indigo-600 dark:text-indigo-400">{{ $sector['value'] }}</span>
                        </div>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $sector['subtitle'] }}</p>
                    </div>
                    @endforeach
                </div>
            </x-ui.card>
        </div>
    </div>
</section>
@endsection