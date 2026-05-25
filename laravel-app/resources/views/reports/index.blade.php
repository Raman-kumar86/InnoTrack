@extends('layouts.app')

@section('content')
<section class="flex min-h-screen flex-col gap-6" x-data="reportsPage()" x-init="init()">
    <style>
        /* Improve numeric contrast in dark mode for this reports page */
        .dark .reports-contrast {
            color: #f8fafc !important;
        }

        /* Custom thin scrollbar for the reports metrics panel */
        .reports-scroll {
            scrollbar-width: thin;
            scrollbar-color: rgba(148, 163, 184, 0.5) transparent;
        }

        .reports-scroll::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .reports-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .reports-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(148, 163, 184, 0.35);
            border-radius: 8px;
            border: 2px solid transparent;
            background-clip: content-box;
        }

        .dark .reports-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(148, 163, 184, 0.22);
        }
    </style>
    <x-ui.section-header
        title="Reports"
        subtitle="Export, compare, and print executive-ready summaries for government and startup ecosystem reviews.">
        <x-ui.button href="{{ route('reports.export.executive', ['fiscal_year' => $selectedFy]) }}" variant="secondary">
            <x-ui.icon name="download" class="h-4 w-4" />
            Download PDF
        </x-ui.button>
        <x-ui.button type="button" onclick="window.print()">
            <x-ui.icon name="file-text" class="h-4 w-4" />
            Generate report
        </x-ui.button>
    </x-ui.section-header>

    <x-ui.card>
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-mono uppercase tracking-wide text-slate-400 dark:text-slate-500">Fiscal Year</label>
                <select name="fiscal_year" class="select-modern min-w-45" onchange="this.form.submit()">
                    @foreach ($fiscalYears as $fy)
                    <option value="{{ $fy['value'] }}" @selected($selectedFy===$fy['value'])>{{ $fy['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <p class="pb-2 text-xs text-slate-400 dark:text-slate-500">
                Showing data for <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $selectedFyLabel }}</span>
            </p>
        </form>
    </x-ui.card>

    @php
    $tiles = [
    ['label' => 'Total Funding', 'value' => '₹' . number_format($summary['total_funding_cr'], 1) . ' Cr', 'icon' => 'trending-up', 'border' => 'border-cyan-500/40'],
    ['label' => 'YoY Growth', 'value' => ($summary['yoy_growth'] >= 0 ? '+' : '') . $summary['yoy_growth'] . '%', 'icon' => 'activity', 'border' => $summary['yoy_direction'] === 'up' ? 'border-green-500/40' : 'border-rose-500/40', 'color' => $summary['yoy_direction'] === 'up' ? 'text-green-600 dark:text-green-400' : 'text-rose-600 dark:text-rose-400'],
    ['label' => 'Approved Grants', 'value' => number_format($summary['approved_grants']), 'icon' => 'shield', 'border' => 'border-amber-500/40'],
    ['label' => 'Printable Reports', 'value' => number_format($summary['printable_reports']), 'icon' => 'file-text', 'border' => 'border-purple-500/40'],
    ];
    @endphp

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($tiles as $tile)
        <x-ui.card class="border-l-4 {{ $tile['border'] }}">
            <div class="mb-3 flex items-start justify-between">
                <span class="text-[10px] font-mono uppercase tracking-widest text-slate-400 dark:text-slate-500">{{ $tile['label'] }}</span>
                <x-ui.icon name="{{ $tile['icon'] }}" class="h-4 w-4 text-slate-300 dark:text-slate-600" />
            </div>
            <div class="text-3xl font-bold leading-none text-slate-900 dark:text-white reports-contrast {{ $tile['color'] ?? '' }}">{{ $tile['value'] }}</div>
        </x-ui.card>
        @endforeach
    </div>

    <div class="flex-1 min-h-0 items-stretch gap-6 xl:grid-cols-3">
        <x-ui.card class="flex h-full min-h-0 flex-col xl:col-span-2">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Year-over-year comparison</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Financial and operational growth trends across reporting periods.</p>
                </div>
                <x-ui.badge variant="info">FY comparison</x-ui.badge>
            </div>

            <div class="mt-6 flex flex-1 min-h-0 flex-col gap-4">
                <div class="grid flex-1 min-h-0 gap-4 md:grid-cols-2">
                    <div class="flex min-h-0 flex-col rounded-3xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900/60">
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <div>
                                <h4 class="font-semibold text-slate-900 dark:text-white">Funding growth</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Funding accumulation trend.</p>
                            </div>
                        </div>

                        <p class="mb-3 text-xs text-slate-400 dark:text-slate-500">Showing: {{ $selectedFyLabel }}</p>
                        <div class="relative flex-1 min-h-[380px] w-full">
                            <canvas id="fundingChart" class="!h-full !w-full"></canvas>
                        </div>
                    </div>

                    <div class="flex min-h-0 flex-col rounded-3xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900/60">
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <div>
                                <h4 class="font-semibold text-slate-900 dark:text-white">Grant approval rate</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Reportable approval momentum.</p>
                            </div>
                        </div>

                        <p class="mb-3 text-xs text-slate-400 dark:text-slate-500">Showing: {{ $selectedFyLabel }}</p>
                        <div class="relative flex-1 min-h-[380px] w-full">
                            <canvas id="grantChart" class="!h-full !w-full"></canvas>
                        </div>
                    </div>
                </div>


            </div>


        </x-ui.card>

    </div>
    <div class="grid gap-4 md:grid-cols-2 items-stretch">

        <!-- YEAR OVER YEAR METRICS -->
        <x-ui.card class="h-full flex flex-col rounded-3xl border border-white/5 bg-slate-900/80 p-5 backdrop-blur-xl">

            <div class="mb-4 shrink-0">
                <h3 class="font-semibold text-white">
                    Year over year metrics
                </h3>

                <p class="mt-1 text-sm text-slate-400">
                    Side-by-side comparison across fiscal years.
                </p>
            </div>

            <!-- SCROLLABLE CONTENT -->
            <div class="space-y-4 overflow-y-auto pr-2 max-h-[420px] reports-scroll flex-1">

                @php
                $metricLabels = [
                'total_funding_cr' => 'Total Funding (Cr)',
                'active_startups' => 'Active Startups',
                'registrations' => 'Registrations',
                'dpiit_recognized' => 'DPIIT Recognized',
                'jobs_created' => 'Jobs Created',
                'women_led' => 'Women-led Startups',
                ];
                @endphp

                @foreach ($yoy['comparison'] as $row)

                @php
                $label = $metricLabels[$row['metric']] ?? $row['metric'];

                $max = max(
                $row['fy1_value'],
                $row['fy2_value'],
                1
                );

                $pct1 = round(($row['fy1_value'] / $max) * 100);
                $pct2 = round(($row['fy2_value'] / $max) * 100);
                @endphp

                <div class="rounded-2xl border border-white/5 bg-slate-950/40 p-4 transition hover:border-cyan-500/20 hover:bg-slate-900/70">

                    <!-- HEADER -->
                    <div class="mb-3 flex items-center justify-between">

                        <span class="text-xs font-medium text-slate-300">
                            {{ $label }}
                        </span>

                        <span class="text-xs font-mono {{ $row['direction'] === 'up'
                        ? 'text-emerald-400'
                        : 'text-yellow-500' }}">

                            {{ $row['direction'] === 'up' ? '↑' : '↓' }}
                            {{ abs($row['change']) }}%
                        </span>
                    </div>

                    <!-- FY1 -->
                    <div class="mb-3">

                        <div class="mb-1 flex items-center justify-between">

                            <span class="text-[10px] font-mono text-slate-500">
                                {{ $row['fy1_label'] }}
                            </span>

                            <span class="text-[10px] font-mono text-slate-300">
                                {{ number_format($row['fy1_value'], is_float($row['fy1_value']) ? 1 : 0) }}
                            </span>
                        </div>

                        <div class="h-2 overflow-hidden rounded-full bg-slate-800">

                            <div
                                class="h-full rounded-full bg-cyan-400 transition-all duration-700"
                                x-bind:style="{ width: '{{ $pct1 }}%' }">
                            </div>
                        </div>
                    </div>

                    <!-- FY2 -->
                    <div>

                        <div class="mb-1 flex items-center justify-between">

                            <span class="text-[10px] font-mono text-slate-500">
                                {{ $row['fy2_label'] }}
                            </span>

                            <span class="text-[10px] font-mono text-slate-300">
                                {{ number_format($row['fy2_value'], is_float($row['fy2_value']) ? 1 : 0) }}
                            </span>
                        </div>

                        <div class="h-2 overflow-hidden rounded-full bg-slate-800">

                            <div
                                class="h-full rounded-full bg-indigo-400 transition-all duration-700"
                                x-bind:style="{ width: '{{ $pct2 }}%' }">
                            </div>
                        </div>
                    </div>
                </div>

                @endforeach
            </div>
        </x-ui.card>

        <!-- EXPORT CENTER -->
        <x-ui.card class="h-full flex flex-col rounded-3xl border border-white/5 bg-slate-900/80 p-5 backdrop-blur-xl">

            <div class="mb-4 shrink-0">

                <h3 class="text-lg font-semibold text-white">
                    Export center
                </h3>

                <p class="mt-1 text-sm text-slate-400">
                    Export executive reports and analytics summaries.
                </p>
            </div>

            <!-- SCROLLABLE EXPORTS -->
            <div class="space-y-3 overflow-y-auto pr-2 max-h-[420px] reports-scroll flex-1">

                @foreach ($exportFormats as $key => $fmt)

                <a
                    href="{{ route($fmt['route'], ['fiscal_year' => $selectedFy]) }}"
                    class="group flex items-start gap-4 rounded-2xl border border-white/5 bg-slate-950/40 p-5 transition hover:border-cyan-500/20 hover:bg-slate-900/70">

                    <!-- ICON -->
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-500/10 transition group-hover:bg-indigo-500/20">

                        <x-ui.icon
                            name="{{ $fmt['icon'] }}"
                            class="h-5 w-5 text-indigo-400" />
                    </div>

                    <!-- TEXT -->
                    <div class="min-w-0 flex-1">

                        <p class="font-semibold text-sm text-white">
                            {{ $fmt['label'] }}
                        </p>

                        <p class="mt-0.5 text-xs text-slate-400">
                            {{ $fmt['hint'] }}
                        </p>
                    </div>
                </a>

                @endforeach
            </div>
        </x-ui.card>
    </div>
    <x-ui.card class="border border-white/5 bg-slate-900/80 backdrop-blur-xl">

        <!-- HEADER -->
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-white">
                Monthly detail
            </h3>

            <p class="mt-1 text-sm text-slate-400">
                Per-month funding, registrations and approval rates for the selected range.
            </p>
        </div>

        <!-- TABLE -->
        <div class="mt-4 overflow-x-auto reports-scroll rounded-2xl border border-white/5">

            <table class="w-full table-auto border-collapse text-sm">

                <!-- TABLE HEAD -->
                <thead class="sticky top-0 z-10 bg-slate-950/95 backdrop-blur">

                    <tr class="text-left text-xs uppercase tracking-wide text-slate-400">

                        <th class="px-4 py-3 font-semibold">
                            Month
                        </th>

                        <th class="px-4 py-3 text-right font-semibold">
                            Funding (USD)
                        </th>

                        <th class="px-4 py-3 text-right font-semibold">
                            Cumulative (USD)
                        </th>

                        <th class="px-4 py-3 text-right font-semibold">
                            Registrations
                        </th>

                        <th class="px-4 py-3 text-right font-semibold">
                            Recognized
                        </th>

                        <th class="px-4 py-3 text-right font-semibold">
                            Approval %
                        </th>
                    </tr>
                </thead>

                <!-- TABLE BODY -->
                <tbody class="divide-y divide-white/5">

                    @forelse ($monthlyDetail as $row)

                    <tr class="transition hover:bg-slate-800/40">

                        <!-- MONTH -->
                        <td class="px-4 py-3 font-medium text-white">
                            {{ $row['month_label'] }}
                        </td>

                        <!-- FUNDING -->
                        <td class="px-4 py-3 text-right font-mono text-sm text-slate-300">

                            @php
                            $fundingUsd = (float) $row['funding_usd'];

                            echo match (true) {
                            $fundingUsd >= 1e9 => '$' . number_format($fundingUsd / 1e9, 2) . 'B',
                            $fundingUsd >= 1e6 => '$' . number_format($fundingUsd / 1e6, 1) . 'M',
                            $fundingUsd >= 1e3 => '$' . number_format($fundingUsd / 1e3, 0) . 'K',
                            $fundingUsd > 0 => '$' . number_format($fundingUsd, 2),
                            default => '—',
                            };
                            @endphp
                        </td>

                        <!-- CUMULATIVE -->
                        <td class="px-4 py-3 text-right font-mono text-sm text-cyan-400">

                            @php
                            $cumulativeCr = (float) $row['cumulative_usd'];

                            echo $cumulativeCr > 0
                            ? '₹' . number_format($cumulativeCr, 1) . ' Cr'
                            : '—';
                            @endphp
                        </td>

                        <!-- REGISTRATIONS -->
                        <td class="px-4 py-3 text-right font-mono text-sm text-slate-300">
                            {{ $row['registrations'] > 0 ? number_format($row['registrations']) : '—' }}
                        </td>

                        <!-- RECOGNIZED -->
                        <td class="px-4 py-3 text-right font-mono text-sm text-emerald-400">
                            {{ $row['recognized'] > 0 ? number_format($row['recognized']) : '—' }}
                        </td>

                        <!-- APPROVAL -->
                        <td class="px-4 py-3 text-right">

                            <div class="flex items-center justify-end gap-2">

                                <div class="h-1.5 w-14 overflow-hidden rounded-full bg-slate-800">

                                    <div
                                        class="h-full rounded-full bg-emerald-400"
                                        x-bind:style="{ width: '{{ min(100, $row['approval_pct']) }}%' }">
                                    </div>
                                </div>

                                <span class="font-mono text-sm
                                {{
                                    $row['approval_pct'] >= 50
                                    ? 'text-emerald-400'
                                    : ($row['approval_pct'] > 0
                                        ? 'text-yellow-500'
                                        : 'text-slate-500')
                                }}">

                                    {{ $row['approval_pct'] > 0 ? $row['approval_pct'] . '%' : '0.0%' }}
                                </span>
                            </div>
                        </td>
                    </tr>

                    @empty

                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-sm text-slate-500">
                            No data for the selected range.
                        </td>
                    </tr>

                    @endforelse

                    <!-- TOTAL ROW -->
                    @if (count($monthlyDetail) > 0)

                    <tr class="border-t border-cyan-500/20 bg-gradient-to-r from-cyan-500/10 via-indigo-500/10 to-slate-900">

                        <!-- TOTAL LABEL -->
                        <td class="px-4 py-4 text-xs font-mono font-bold uppercase tracking-[0.2em] text-cyan-300">
                            Total
                        </td>

                        <!-- TOTAL FUNDING -->
                        <td class="px-4 py-4 text-right font-mono font-bold text-sm text-white">

                            @php
                            $totalUsd = collect($monthlyDetail)->sum('funding_usd');

                            echo match (true) {
                            $totalUsd >= 1e9 => '$' . number_format($totalUsd / 1e9, 2) . 'B',
                            $totalUsd >= 1e6 => '$' . number_format($totalUsd / 1e6, 1) . 'M',
                            $totalUsd > 0 => '$' . number_format($totalUsd, 0),
                            default => '—',
                            };
                            @endphp
                        </td>

                        <!-- TOTAL CUMULATIVE -->
                        <td class="px-4 py-4 text-right font-mono font-bold text-sm text-cyan-400">
                            ₹{{ number_format(collect($monthlyDetail)->last()['cumulative_usd'] ?? 0, 1) }} Cr
                        </td>

                        <!-- TOTAL REG -->
                        <td class="px-4 py-4 text-right font-mono font-bold text-sm text-slate-200">
                            {{ number_format(collect($monthlyDetail)->sum('registrations')) }}
                        </td>

                        <!-- TOTAL RECOGNIZED -->
                        <td class="px-4 py-4 text-right font-mono font-bold text-sm text-emerald-400">
                            {{ number_format(collect($monthlyDetail)->sum('recognized')) }}
                        </td>

                        <!-- TOTAL APPROVAL -->
                        <td class="px-4 py-4 text-right font-mono font-bold text-sm text-cyan-300">

                            @php
                            $totalRegistrations = collect($monthlyDetail)->sum('registrations');

                            $totalRecognized = collect($monthlyDetail)->sum('recognized');

                            echo $totalRegistrations > 0
                            ? round(($totalRecognized / $totalRegistrations) * 100, 1) . '%'
                            : '0.0%';
                            @endphp
                        </td>
                    </tr>

                    @endif
                </tbody>
            </table>
        </div>
    </x-ui.card>
</section>
@endsection

@push('scripts')
<script type="application/json" id="reports-chart-data">
    @json([
        'funding' => $fundingGrowth['months'],
        'grant' => $grantApproval['months'],
    ])
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    function reportsPage() {
        const chartDataElement = document.getElementById('reports-chart-data');
        const chartData = chartDataElement?.textContent ? JSON.parse(chartDataElement.textContent) : {
            funding: [],
            grant: []
        };

        return {
            fundingData: chartData.funding || [],
            grantData: chartData.grant || [],
            charts: {
                funding: null,
                grant: null
            },

            init() {
                this.$nextTick(() => {
                    this.buildFundingChart();
                    this.buildGrantChart();
                });
            },

            buildFundingChart() {
                const canvas = document.getElementById('fundingChart');
                if (!canvas) return;

                if (this.charts.funding) {
                    this.charts.funding.destroy();
                }

                const labels = this.fundingData.map((month) => month.month_label);
                const monthly = this.fundingData.map((month) => month.funding_cr);
                const cumulative = this.fundingData.map((month) => month.cumulative_cr);

                this.charts.funding = new Chart(canvas, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                                type: 'bar',
                                label: 'Monthly (₹ Cr)',
                                data: monthly,
                                backgroundColor: 'rgba(52,211,153,0.5)',
                                borderRadius: 4,
                                order: 2,
                            },
                            {
                                type: 'line',
                                label: 'Cumulative (₹ Cr)',
                                data: cumulative,
                                borderColor: '#6ee7f7',
                                backgroundColor: 'rgba(110,231,247,0.08)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 3,
                                pointBackgroundColor: '#6ee7f7',
                                order: 1,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                onClick: (e, legendItem, legend) => {
                                    const index = legendItem.datasetIndex;
                                    const ci = legend.chart;
                                    const meta = ci.getDatasetMeta(index);
                                    // toggle visibility
                                    meta.hidden = meta.hidden === null ? !ci.data.datasets[index].hidden : null;
                                    ci.update();
                                },
                                onHover: (e) => {
                                    if (e?.native?.target) e.native.target.style.cursor = 'pointer';
                                },
                                onLeave: (e) => {
                                    if (e?.native?.target) e.native.target.style.cursor = 'default';
                                },
                                labels: {
                                    font: {
                                        family: 'monospace',
                                        size: 10
                                    },
                                    color: 'rgba(148,163,184,0.9)',
                                    boxWidth: 12,
                                    boxHeight: 12,
                                },
                            },
                            tooltip: {
                                enabled: true,
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: (context) => context.dataset.label + ': ₹' + context.raw + ' Cr',
                                },
                            },
                        },
                        scales: {
                            x: {
                                ticks: {
                                    font: {
                                        family: 'monospace',
                                        size: 10
                                    },
                                    color: 'rgba(148,163,184,0.7)'
                                },
                                grid: {
                                    color: 'rgba(255,255,255,0.04)'
                                },
                            },
                            y: {
                                ticks: {
                                    font: {
                                        family: 'monospace',
                                        size: 10
                                    },
                                    color: 'rgba(148,163,184,0.7)',
                                    callback: (value) => '₹' + value + ' Cr'
                                },
                                grid: {
                                    color: 'rgba(255,255,255,0.04)'
                                },
                            },
                        },
                    },
                });
            },

            buildGrantChart() {
                const canvas = document.getElementById('grantChart');
                if (!canvas) return;

                if (this.charts.grant) {
                    this.charts.grant.destroy();
                }

                const labels = this.grantData.map((month) => month.month_label);
                const registrations = this.grantData.map((month) => month.registrations);
                const recognized = this.grantData.map((month) => month.recognized);
                const approvalRates = this.grantData.map((month) => month.approval_rate);

                this.charts.grant = new Chart(canvas, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                                type: 'bar',
                                label: 'Registrations',
                                data: registrations,
                                backgroundColor: 'rgba(167,139,250,0.4)',
                                borderRadius: 4,
                                order: 2,
                            },
                            {
                                type: 'bar',
                                label: 'Recognized',
                                data: recognized,
                                backgroundColor: 'rgba(52,211,153,0.6)',
                                borderRadius: 4,
                                order: 3,
                            },
                            {
                                type: 'line',
                                label: 'Approval %',
                                data: approvalRates,
                                borderColor: '#fbbf24',
                                borderWidth: 2,
                                fill: false,
                                tension: 0.4,
                                pointRadius: 3,
                                pointBackgroundColor: '#fbbf24',
                                yAxisID: 'y2',
                                order: 1,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                onClick: (e, legendItem, legend) => {
                                    const index = legendItem.datasetIndex;
                                    const ci = legend.chart;
                                    const meta = ci.getDatasetMeta(index);
                                    meta.hidden = meta.hidden === null ? !ci.data.datasets[index].hidden : null;
                                    ci.update();
                                },
                                onHover: (e) => {
                                    if (e?.native?.target) e.native.target.style.cursor = 'pointer';
                                },
                                onLeave: (e) => {
                                    if (e?.native?.target) e.native.target.style.cursor = 'default';
                                },
                                labels: {
                                    font: {
                                        family: 'monospace',
                                        size: 10
                                    },
                                    color: 'rgba(148,163,184,0.9)',
                                    boxWidth: 12,
                                    boxHeight: 12,
                                },
                            },
                            tooltip: {
                                enabled: true,
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: (context) => {
                                        if (context.dataset.label === 'Approval %') {
                                            return 'Approval: ' + context.raw + '%';
                                        }

                                        return context.dataset.label + ': ' + context.raw;
                                    },
                                },
                            },
                        },
                        scales: {
                            x: {
                                ticks: {
                                    font: {
                                        family: 'monospace',
                                        size: 10
                                    },
                                    color: 'rgba(148,163,184,0.7)'
                                },
                                grid: {
                                    color: 'rgba(255,255,255,0.04)'
                                },
                            },
                            y: {
                                ticks: {
                                    font: {
                                        family: 'monospace',
                                        size: 10
                                    },
                                    color: 'rgba(148,163,184,0.7)'
                                },
                                grid: {
                                    color: 'rgba(255,255,255,0.04)'
                                },
                            },
                            y2: {
                                position: 'right',
                                ticks: {
                                    font: {
                                        family: 'monospace',
                                        size: 10
                                    },
                                    color: '#fbbf24',
                                    callback: (value) => value + '%'
                                },
                                grid: {
                                    display: false
                                },
                            },
                        },
                    },
                });
            },
        };
    }
</script>
@endpush