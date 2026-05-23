@extends('layouts.app')

@php
    $title = 'State Analytics';
    $pageTitle = 'State Analytics';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'State Analytics', 'url' => route('analytics.state')],
    ];

    $stateLabels = ['Karnataka', 'Maharashtra', 'Delhi', 'Telangana', 'Tamil Nadu', 'Gujarat'];
    $fundingValues = [480, 455, 410, 388, 365, 342];
    $sectorLabels = ['SaaS', 'FinTech', 'HealthTech', 'Agritech', 'Deep Tech'];
    $sectorValues = [42, 20, 18, 12, 8];

    $rankings = [
        ['name' => 'Karnataka', 'startups' => 4812, 'funding' => 'Rs 920 Cr', 'growth' => '+12.5%'],
        ['name' => 'Maharashtra', 'startups' => 4308, 'funding' => 'Rs 880 Cr', 'growth' => '+10.2%'],
        ['name' => 'Delhi', 'startups' => 2890, 'funding' => 'Rs 660 Cr', 'growth' => '+9.7%'],
        ['name' => 'Telangana', 'startups' => 2661, 'funding' => 'Rs 610 Cr', 'growth' => '+8.9%'],
        ['name' => 'Tamil Nadu', 'startups' => 2520, 'funding' => 'Rs 585 Cr', 'growth' => '+8.1%'],
    ];
@endphp

@section('content')
<section class="space-y-6">
    <x-ui.section-header
        title="State analytics"
        subtitle="Compare startup activity, funding depth, and sector concentration across Indian states."
    >
        <x-ui.button variant="secondary">Quarterly</x-ui.button>
        <x-ui.button variant="secondary">Financial year</x-ui.button>
        <x-ui.button>Apply filters</x-ui.button>
    </x-ui.section-header>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
        <x-ui.kpi-card title="Active states" value="28" trend="2.4%" icon="map" :spark-labels="['Q1','Q2','Q3','Q4','Q5','Q6']" :spark-values="[12,14,16,18,22,28]" />
        <x-ui.kpi-card title="High-growth states" value="11" trend="4.1%" icon="arrow-up" :spark-labels="['Q1','Q2','Q3','Q4','Q5','Q6']" :spark-values="[3,5,6,7,9,11]" />
        <x-ui.kpi-card title="Funding concentration" value="Rs 4,520 Cr" trend="11.8%" icon="funding" :spark-labels="['Q1','Q2','Q3','Q4','Q5','Q6']" :spark-values="[280,320,350,390,420,470]" />
        <x-ui.kpi-card title="Sector clusters" value="5" trend="1.0%" icon="grid" :spark-labels="['Q1','Q2','Q3','Q4','Q5','Q6']" :spark-values="[2,3,3,4,4,5]" />
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <x-ui.card class="xl:col-span-2">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">India map visualization</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">A visual state intelligence view for funding density and ecosystem maturity.</p>
                </div>
                <x-ui.badge variant="info">Live</x-ui.badge>
            </div>

            <div class="relative mt-6 overflow-hidden rounded-[2rem] border border-slate-200 bg-gradient-to-br from-slate-50 via-white to-indigo-50 p-6 dark:border-slate-800 dark:from-slate-900 dark:via-slate-950 dark:to-indigo-950/30">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(79,70,229,0.18),transparent_30%),radial-gradient(circle_at_bottom_right,rgba(14,165,233,0.16),transparent_28%)]"></div>
                <div class="relative grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(320px,1fr)]">
                    <div class="relative min-h-[360px] overflow-hidden rounded-[2rem] border border-white/70 bg-white/70 p-5 shadow-lg backdrop-blur dark:border-slate-800 dark:bg-slate-900/80">
                        <div class="absolute inset-8 rounded-[50%_50%_42%_42%/58%_58%_42%_42%] border border-indigo-500/15 bg-gradient-to-br from-indigo-500/8 to-cyan-500/8"></div>
                        <div class="absolute left-[18%] top-[22%] rounded-2xl bg-indigo-600 px-3 py-2 text-xs font-semibold text-white shadow-lg shadow-indigo-600/30">Karnataka</div>
                        <div class="absolute left-[49%] top-[12%] rounded-2xl bg-cyan-600 px-3 py-2 text-xs font-semibold text-white shadow-lg shadow-cyan-600/30">Delhi</div>
                        <div class="absolute left-[58%] top-[35%] rounded-2xl bg-emerald-600 px-3 py-2 text-xs font-semibold text-white shadow-lg shadow-emerald-600/30">Maharashtra</div>
                        <div class="absolute left-[29%] top-[55%] rounded-2xl bg-slate-700 px-3 py-2 text-xs font-semibold text-white shadow-lg shadow-slate-900/20">Tamil Nadu</div>
                        <div class="absolute left-[15%] top-[45%] rounded-2xl bg-blue-600 px-3 py-2 text-xs font-semibold text-white shadow-lg shadow-blue-600/30">Telangana</div>
                        <div class="absolute bottom-6 left-6 rounded-2xl border border-slate-200 bg-white/90 px-4 py-3 text-sm text-slate-600 dark:border-slate-800 dark:bg-slate-950/90 dark:text-slate-300">
                            <p class="font-semibold text-slate-900 dark:text-white">Top state</p>
                            <p class="mt-1">Karnataka leads in registered startups and funding depth.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="rounded-3xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Filtered state insights</p>
                            <div class="mt-4 space-y-3">
                                <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-950/80"><span>Karnataka</span><span class="font-semibold text-slate-900 dark:text-white">Rs 920 Cr</span></div>
                                <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-950/80"><span>Maharashtra</span><span class="font-semibold text-slate-900 dark:text-white">Rs 880 Cr</span></div>
                                <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-950/80"><span>Delhi</span><span class="font-semibold text-slate-900 dark:text-white">Rs 660 Cr</span></div>
                            </div>
                        </div>
                        <div class="rounded-3xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Interactive filters</p>
                            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                                <button class="rounded-2xl border border-slate-200 px-4 py-3 text-left text-sm transition hover:border-indigo-300 hover:bg-indigo-50 dark:border-slate-800 dark:hover:bg-slate-950/80">Funding intensity</button>
                                <button class="rounded-2xl border border-slate-200 px-4 py-3 text-left text-sm transition hover:border-indigo-300 hover:bg-indigo-50 dark:border-slate-800 dark:hover:bg-slate-950/80">Sector density</button>
                                <button class="rounded-2xl border border-slate-200 px-4 py-3 text-left text-sm transition hover:border-indigo-300 hover:bg-indigo-50 dark:border-slate-800 dark:hover:bg-slate-950/80">DPIIT recognition</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">State statistics</h3>
            <div class="mt-5 space-y-4">
                @foreach ($rankings as $state)
                    <div class="rounded-3xl border border-slate-200 p-4 dark:border-slate-800">
                        <div class="flex items-center justify-between gap-3">
                            <p class="font-medium text-slate-900 dark:text-white">{{ $state['name'] }}</p>
                            <x-ui.badge variant="success">{{ $state['growth'] }}</x-ui.badge>
                        </div>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ number_format($state['startups']) }} startups</p>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $state['funding'] }} total funding</p>
                    </div>
                @endforeach
            </div>
        </x-ui.card>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <x-ui.chart-card title="Funding comparison by state" subtitle="Compare funding depth using a horizontal bar chart.">
            <canvas data-chart="bar" data-horizontal="true" data-labels='@json($stateLabels)' data-values='@json($fundingValues)'></canvas>
        </x-ui.chart-card>

        <x-ui.chart-card title="Sector distribution by state" subtitle="Dominant sector mix used by the state ecosystem filter.">
            <canvas data-chart="doughnut" data-labels='@json($sectorLabels)' data-datasets='@json([["data" => $sectorValues, "backgroundColor" => ["#4f46e5", "#0ea5e9", "#10b981", "#f59e0b", "#64748b"]]])'></canvas>
        </x-ui.chart-card>
    </div>

    <x-ui.table title="State ranking table" subtitle="Sortable ranking table with funding and growth indicators.">
        <thead class="table-head">
            <tr>
                <th class="px-6 py-4 font-semibold">State</th>
                <th class="px-6 py-4 font-semibold">Startups</th>
                <th class="px-6 py-4 font-semibold">Funding</th>
                <th class="px-6 py-4 font-semibold">Growth</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
            @foreach ($rankings as $state)
                <tr>
                    <td class="table-cell font-medium text-slate-900 dark:text-white">{{ $state['name'] }}</td>
                    <td class="table-cell">{{ number_format($state['startups']) }}</td>
                    <td class="table-cell">{{ $state['funding'] }}</td>
                    <td class="table-cell"><x-ui.badge variant="success">{{ $state['growth'] }}</x-ui.badge></td>
                </tr>
            @endforeach
        </tbody>
    </x-ui.table>
</section>
@endsection
