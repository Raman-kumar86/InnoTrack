@extends('layouts.app')

@section('content')
<section class="space-y-6" x-data="stateAnalytics()" x-init="init()">
    <x-ui.section-header
        title="State analytics"
        subtitle="Compare startup activity, funding depth, and sector concentration across Indian states."
    >
        <x-ui.button
            href="{{ route('state-analytics.export', ['fiscal_year' => $appliedFilters['fiscal_year'], 'quarter' => $appliedFilters['quarter']]) }}"
            variant="secondary"
        >
            <x-ui.icon name="download" class="h-4 w-4" />
            Export CSV
        </x-ui.button>
    </x-ui.section-header>

    <x-ui.card>
        <form method="GET" action="{{ route('state-analytics.index') }}" id="filterForm">
            <div class="flex flex-wrap items-end gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-medium font-mono uppercase tracking-wide text-slate-400 dark:text-slate-500">
                        Quarter
                    </label>
                    <select name="quarter" class="select-modern min-w-45" onchange="this.form.submit()">
                        @foreach ($quarters as $q)
                            <option value="{{ $q['value'] }}" {{ $appliedFilters['quarter'] === $q['value'] ? 'selected' : '' }}>
                                {{ $q['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-medium font-mono uppercase tracking-wide text-slate-400 dark:text-slate-500">
                        Fiscal Year
                    </label>
                    <select name="fiscal_year" class="select-modern min-w-45" onchange="this.form.submit()">
                        @foreach ($fiscalYears as $fy)
                            <option value="{{ $fy['value'] }}" {{ $appliedFilters['fiscal_year'] === $fy['value'] ? 'selected' : '' }}>
                                {{ $fy['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end pb-0.5">
                    <span class="text-xs text-slate-400 dark:text-slate-500">
                        Showing data for
                        <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $appliedFilters['quarter_label'] }}</span>
                        <span class="mx-1 text-slate-300 dark:text-slate-600">|</span>
                        <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $appliedFilters['fiscal_year_label'] }}</span>
                        <span class="mx-1 text-slate-300 dark:text-slate-600">|</span>
                        {{ $appliedFilters['date_range'] }}
                    </span>
                </div>

                @if ($appliedFilters['quarter'] !== $defaultQuarter || $appliedFilters['fiscal_year'] !== $defaultFiscalYear)
                    <a
                        href="{{ route('state-analytics.index') }}"
                        class="pb-2 text-xs font-medium text-rose-500 underline underline-offset-2 hover:text-rose-600"
                    >
                        Reset filters
                    </a>
                @endif
            </div>
        </form>
    </x-ui.card>

    @php
        $tiles = [
            [
                'key' => 'active_states',
                'label' => 'ACTIVE STATES',
                'sub' => 'vs previous period',
                'icon' => 'map',
                'border' => 'border-cyan-500/40',
                'value_fmt' => $summary['active_states']['value'],
                'change' => $summary['active_states']['formatted'],
                'dir' => $summary['active_states']['direction'],
            ],
            [
                'key' => 'high_growth_states',
                'label' => 'HIGH-GROWTH STATES',
                'sub' => 'avg growth > ' . config('analytics.high_growth_threshold') . '%',
                'icon' => 'trending-up',
                'border' => 'border-green-500/40',
                'value_fmt' => $summary['high_growth_states']['value'],
                'change' => $summary['high_growth_states']['formatted'],
                'dir' => $summary['high_growth_states']['direction'],
            ],
            [
                'key' => 'funding_concentration',
                'label' => 'FUNDING CONCENTRATION',
                'sub' => 'total raised this period',
                'icon' => 'dollar-sign',
                'border' => 'border-amber-500/40',
                'value_fmt' => '₹' . number_format($summary['funding_concentration']['value']) . ' Cr',
                'change' => $summary['funding_concentration']['formatted'],
                'dir' => $summary['funding_concentration']['direction'],
            ],
            [
                'key' => 'sector_clusters',
                'label' => 'SECTOR CLUSTERS',
                'sub' => 'dominant sector groups',
                'icon' => 'grid',
                'border' => 'border-purple-500/40',
                'value_fmt' => $summary['sector_clusters']['value'],
                'change' => $summary['sector_clusters']['formatted'],
                'dir' => $summary['sector_clusters']['direction'],
            ],
        ];
    @endphp

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($tiles as $tile)
            <x-ui.card class="border-l-4 {{ $tile['border'] }}">
                <div class="mb-3 flex items-start justify-between">
                    <span class="text-[10px] font-mono uppercase tracking-widest text-slate-400 dark:text-slate-500">
                        {{ $tile['label'] }}
                    </span>
                    <x-ui.icon name="{{ $tile['icon'] }}" class="h-4 w-4 text-slate-300 dark:text-slate-600" />
                </div>

                <div class="mb-2 text-3xl font-bold leading-none text-slate-900 dark:text-white">
                    {{ $tile['value_fmt'] }}
                </div>

                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-mono font-medium {{ $tile['dir'] === 'up' ? 'bg-green-100 text-green-700 dark:bg-green-950/40 dark:text-green-400' : 'bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400' }}">
                        {{ $tile['dir'] === 'up' ? '↑' : '↓' }}
                        {{ $tile['change'] }}
                    </span>
                    <span class="text-xs text-slate-400 dark:text-slate-500">{{ $tile['sub'] }}</span>
                </div>
            </x-ui.card>
        @endforeach
    </div>

    <x-ui.card padding="none">
        @php
            $tabs = [
                ['key' => 'overview', 'label' => 'Overview', 'icon' => 'activity'],
                ['key' => 'funding', 'label' => 'Funding Map', 'icon' => 'dollar-sign'],
                ['key' => 'sectors', 'label' => 'Sector Heat', 'icon' => 'layers'],
                ['key' => 'table', 'label' => 'Data Table', 'icon' => 'table'],
            ];
        @endphp

        <div class="flex items-center gap-1 border-b border-slate-200 p-2 dark:border-slate-800">
            @foreach ($tabs as $tab)
                <button
                    type="button"
                    @click="activeTab = '{{ $tab['key'] }}'"
                    :class="activeTab === '{{ $tab['key'] }}' ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-semibold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300'"
                    class="flex items-center gap-2 rounded-xl px-4 py-2 text-sm transition"
                >
                    <x-ui.icon name="{{ $tab['icon'] }}" class="h-4 w-4" />
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </div>

        <div x-show="activeTab === 'overview'" x-transition class="p-6">
            <div class="mb-4">
                <h3 class="font-semibold text-slate-900 dark:text-white">Funding by State - Bar Comparison</h3>
                <p class="mt-1 text-sm text-slate-400 dark:text-slate-500">
                    Bar height = funding volume. Hover a bar to inspect state activity and growth.
                </p>
            </div>

            <div class="relative h-180 overflow-visible rounded-3xl border border-slate-200/60 bg-slate-950/20 p-4 dark:border-slate-800">
                <canvas id="overviewChart"></canvas>
            </div>
        </div>

        <div x-show="activeTab === 'funding'" x-transition class="p-6">
            <div class="mb-6 flex flex-wrap gap-3">
                <div class="rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-xs text-slate-600 dark:border-slate-700 dark:bg-slate-800/40 dark:text-slate-300">
                    Top Funded: <span class="font-semibold" x-text="fundingSummary.topFunded"></span>
                </div>
                <div class="rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-xs text-slate-600 dark:border-slate-700 dark:bg-slate-800/40 dark:text-slate-300">
                    Total Deals: <span class="font-semibold" x-text="fundingSummary.totalDeals"></span>
                </div>
                <div class="rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-xs text-slate-600 dark:border-slate-700 dark:bg-slate-800/40 dark:text-slate-300">
                    Unique Investors: <span class="font-semibold" x-text="fundingSummary.uniqueInvestors"></span>
                </div>
            </div>

            <div class="relative h-145">
                <canvas id="fundingChart"></canvas>
            </div>
        </div>

        <div x-show="activeTab === 'sectors'" x-transition class="p-6">
            <p class="mb-4 text-sm text-slate-400 dark:text-slate-500">
                Startup activity breakdown - top 8 states
            </p>

            <div class="relative h-105">
                <canvas id="sectorChart"></canvas>
            </div>

            <div class="mt-4 flex items-center gap-6">
                <span class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="h-3 w-3 rounded-sm bg-blue-500"></span>
                    Total Active
                </span>
                <span class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="h-3 w-3 rounded-sm bg-green-500"></span>
                    High Growth
                </span>
                <span class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="h-3 w-3 rounded-sm bg-purple-500"></span>
                    Women-led
                </span>
            </div>
        </div>

        <div x-show="activeTab === 'table'" x-transition>
            <div class="flex items-center justify-between px-6 pt-4">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    {{ $statesData->count() }} states · Click column headers to sort
                </p>
                <div class="text-xs text-slate-400 dark:text-slate-500">↑↓ = active sort</div>
            </div>

            @php
                $cols = [
                    ['key' => 'state_name', 'label' => 'STATE', 'sortable' => true],
                    ['key' => 'active_startups', 'label' => 'ACTIVE', 'sortable' => true],
                    ['key' => 'high_growth_startups', 'label' => 'HIGH GROWTH', 'sortable' => true],
                    ['key' => 'women_led_startups', 'label' => 'WOMEN-LED', 'sortable' => true],
                    ['key' => 'dpiit_recognized', 'label' => 'DPIIT', 'sortable' => true],
                    ['key' => 'funding_inr_cr', 'label' => 'FUNDING (CR)', 'sortable' => true],
                    ['key' => 'total_deals', 'label' => 'DEALS', 'sortable' => true],
                    ['key' => 'dominant_sector', 'label' => 'DOM. SECTOR', 'sortable' => false],
                    ['key' => 'tier', 'label' => 'TIER', 'sortable' => false],
                ];
            @endphp

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="table-head">
                        <tr>
                            @foreach ($cols as $col)
                                <th
                                    class="px-4 py-3 text-left text-[10px] font-mono uppercase tracking-wider text-slate-400 dark:text-slate-500 {{ $col['sortable'] ? 'cursor-pointer select-none hover:text-indigo-500' : '' }}"
                                    @if ($col['sortable'])
                                        @click="sortTable('{{ $col['key'] }}')"
                                    @endif
                                >
                                    <span class="flex items-center gap-1">
                                        {{ $col['label'] }}
                                        @if ($col['sortable'])
                                            <span x-text="sortIndicator('{{ $col['key'] }}')" class="text-indigo-500"></span>
                                        @endif
                                    </span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <template x-for="state in sortedStates()" :key="state.state_id">
                            <tr class="transition hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <span class="text-sm font-medium text-slate-900 dark:text-white" x-text="state.state_name"></span>
                                        <span class="rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-mono text-slate-400 dark:bg-slate-800 dark:text-slate-500" x-text="state.state_code"></span>
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <span class="font-mono text-sm font-semibold text-cyan-600 dark:text-cyan-400" x-text="Number(state.active_startups).toLocaleString()"></span>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex flex-col items-end gap-1">
                                        <span class="font-mono text-sm text-green-600 dark:text-green-400" x-text="Number(state.high_growth_startups).toLocaleString()"></span>
                                        <div class="h-1.5 w-16 rounded-full bg-slate-100 dark:bg-slate-800" x-show="state.active_startups > 0">
                                            <div
                                                class="h-1.5 rounded-full bg-green-500"
                                                x-bind:style="{ width: Math.min(100, Math.round((state.high_growth_startups / Math.max(state.active_startups, 1)) * 100)) + '%' }"
                                            ></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <span class="font-mono text-sm text-purple-600 dark:text-purple-400" x-text="Number(state.women_led_startups).toLocaleString()"></span>
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <span class="font-mono text-sm text-slate-600 dark:text-slate-300" x-text="Number(state.dpiit_recognized).toLocaleString()"></span>
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <span class="font-mono text-sm text-amber-600 dark:text-amber-400" x-text="fmtINR(state.funding_inr_cr, 1)"></span>
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <span class="font-mono text-sm text-slate-600 dark:text-slate-300" x-text="Number(state.total_deals).toLocaleString()"></span>
                                </td>

                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-mono font-medium"
                                        x-bind:style="{ background: sectorBg(state.dominant_sector), color: sectorColor(state.dominant_sector) }"
                                        x-text="state.dominant_sector"
                                    ></span>
                                </td>

                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-mono font-medium"
                                        x-bind:class="state.tier === 'Tier 1'
                                            ? 'bg-cyan-100 text-cyan-700 dark:bg-cyan-950/40 dark:text-cyan-400'
                                            : (state.tier === 'Tier 2'
                                                ? 'bg-green-100 text-green-700 dark:bg-green-950/40 dark:text-green-400'
                                                : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400')"
                                        x-text="state.tier"
                                    ></span>
                                </td>
                            </tr>
                        </template>

                        <tr class="border-t-2 border-slate-200 bg-slate-50/80 dark:border-slate-700 dark:bg-slate-800/50">
                            <td class="px-4 py-3 font-mono text-sm font-semibold uppercase tracking-wide text-slate-700 dark:text-slate-200">
                                TOTAL
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-cyan-600 dark:text-cyan-400">
                                {{ number_format($statesData->sum('active_startups')) }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-green-600 dark:text-green-400">
                                {{ number_format($statesData->sum('high_growth_startups')) }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-purple-600 dark:text-purple-400">
                                {{ number_format($statesData->sum('women_led_startups')) }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-600 dark:text-slate-300">
                                {{ number_format($statesData->sum('dpiit_recognized')) }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-amber-600 dark:text-amber-400">
                                ₹{{ number_format($statesData->sum('funding_inr_cr'), 1) }} Cr
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-600 dark:text-slate-300">
                                {{ number_format($statesData->sum('total_deals')) }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </x-ui.card>
</section>
@endsection

@push('scripts')
<script type="application/json" id="state-analytics-data">{!! $statesData->values()->toJson() !!}</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
const PALETTE = [
    '#6ee7f7', '#34d399', '#a78bfa', '#fbbf24',
    '#f87171', '#60a5fa', '#f472b6', '#fb923c',
    '#4ade80', '#e879f9', '#38bdf8', '#facc15',
];

function sectorColorFn(name = '') {
    let hash = 0;
    for (const c of name) {
        hash = (hash * 31 + c.charCodeAt(0)) % PALETTE.length;
    }

    return PALETTE[Math.abs(hash)];
}

function sectorBgFn(name = '') {
    return sectorColorFn(name) + '20';
}

function stateAnalytics() {
    const initialStateRows = JSON.parse(document.getElementById('state-analytics-data')?.textContent || '[]');

    return {
        activeTab: 'overview',
        statesData: initialStateRows,
        sortCol: 'active_startups',
        sortDir: 'desc',
        charts: {
            overview: null,
            funding: null,
            sector: null,
        },

        init() {
            this.$watch('activeTab', (tab) => {
                this.$nextTick(() => {
                    if (tab === 'overview') {
                        this.buildOverviewChart();
                    }
                    if (tab === 'funding') {
                        this.buildFundingChart();
                    }
                    if (tab === 'sectors') {
                        this.buildSectorChart();
                    }
                });
            });

            this.$nextTick(() => {
                this.buildOverviewChart();
            });
        },

        fmtINR(value, decimals = 0) {
            const number = Number(value || 0);
            return `₹${number.toLocaleString(undefined, { minimumFractionDigits: decimals, maximumFractionDigits: decimals })} Cr`;
        },

        sectorColor(name) {
            return sectorColorFn(name);
        },

        sectorBg(name) {
            return sectorBgFn(name);
        },

        sortTable(col) {
            if (this.sortCol === col) {
                this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
                return;
            }

            this.sortCol = col;
            this.sortDir = 'desc';
        },

        sortedStates() {
            const sorted = [...this.statesData];

            sorted.sort((a, b) => {
                const av = a[this.sortCol] ?? 0;
                const bv = b[this.sortCol] ?? 0;

                if (typeof av === 'string' || typeof bv === 'string') {
                    return this.sortDir === 'asc'
                        ? String(av).localeCompare(String(bv))
                        : String(bv).localeCompare(String(av));
                }

                return this.sortDir === 'asc' ? av - bv : bv - av;
            });

            return sorted;
        },

        sortIndicator(col) {
            if (this.sortCol !== col) {
                return '⇅';
            }

            return this.sortDir === 'asc' ? '↑' : '↓';
        },

        get fundingSummary() {
            const sorted = [...this.statesData]
                .filter((s) => Number(s.funding_inr_cr) > 0)
                .sort((a, b) => Number(b.funding_inr_cr) - Number(a.funding_inr_cr));

            const top = sorted[0];
            const totalDeals = sorted.reduce((acc, s) => acc + Number(s.total_deals || 0), 0);
            const uniqueInvestors = sorted.reduce((acc, s) => acc + Number(s.unique_investors || 0), 0);

            return {
                topFunded: top ? `${top.state_name} (${this.fmtINR(top.funding_inr_cr, 1)})` : 'N/A',
                totalDeals: totalDeals.toLocaleString(),
                uniqueInvestors: uniqueInvestors.toLocaleString(),
            };
        },

        buildOverviewChart() {
            const canvas = document.getElementById('overviewChart');
            if (!canvas) {
                return;
            }

            if (this.charts.overview) {
                this.charts.overview.destroy();
            }

            const data = this.statesData;
            const sorted = [...data]
                .sort((a, b) => Number(b.funding_inr_cr || 0) - Number(a.funding_inr_cr || 0))
                .slice(0, 12);

            const labels = sorted.map((s) => s.state_name);
            const fundingValues = sorted.map((s) => Number(s.funding_inr_cr || 0));
            const barColors = sorted.map((s) => sectorColorFn(s.dominant_sector || '—') + 'cc');

            this.charts.overview = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Funding (₹ Cr)',
                        data: fundingValues,
                        backgroundColor: barColors,
                        borderColor: barColors,
                        borderWidth: 1,
                        borderRadius: 10,
                        borderSkipped: false,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 16,
                            right: 20,
                            bottom: 12,
                            left: 12,
                        },
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: (ctx) => ctx[0].label,
                                label: (ctx) => {
                                    const state = sorted[ctx.dataIndex];
                                    return [
                                        `Funding: ${this.fmtINR(state.funding_inr_cr, 1)}`,
                                        `Active startups: ${Number(state.active_startups || 0).toLocaleString()}`,
                                        `Avg growth: ${Number(state.avg_growth_pct || 0).toFixed(1)}%`,
                                        `Deals: ${Number(state.total_deals || 0).toLocaleString()}`,
                                        `Sector: ${state.dominant_sector || '—'}`,
                                    ];
                                },
                            },
                        },
                    },
                    scales: {
                        x: {
                            ticks: {
                                maxRotation: 0,
                                autoSkip: false,
                                color: 'rgba(148,163,184,0.95)',
                                font: { family: 'DM Sans', size: 11 },
                            },
                            title: {
                                display: true,
                                text: 'STATE',
                                color: 'rgba(110,231,247,0.7)',
                                font: { family: 'monospace', size: 10 },
                            },
                            grid: { display: false },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                padding: 8,
                                color: 'rgba(148,163,184,0.95)',
                                font: { family: 'monospace', size: 10 },
                                callback: (value) => `₹${value} Cr`,
                            },
                            title: {
                                display: true,
                                text: 'FUNDING (₹ Cr)',
                                color: 'rgba(110,231,247,0.7)',
                                font: { family: 'monospace', size: 10 },
                            },
                            grid: { color: 'rgba(148,163,184,0.15)', drawBorder: false },
                        },
                    },
                    indexAxis: 'x',
                },
            });
        },

        buildFundingChart() {
            const canvas = document.getElementById('fundingChart');
            if (!canvas) {
                return;
            }

            if (this.charts.funding) {
                this.charts.funding.destroy();
            }

            const sorted = [...this.statesData]
                .filter((s) => Number(s.funding_inr_cr) > 0)
                .sort((a, b) => Number(b.funding_inr_cr) - Number(a.funding_inr_cr));

            const maxFunding = Number(sorted[0]?.funding_inr_cr || 1);
            const colors = sorted.map((s) => {
                const opacity = 0.35 + ((Number(s.funding_inr_cr || 0) / maxFunding) * 0.65);
                return `rgba(110,231,247,${opacity})`;
            });

            this.charts.funding = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: sorted.map((s) => s.state_name),
                    datasets: [{
                        label: 'Funding (₹ Cr)',
                        data: sorted.map((s) => Number(s.funding_inr_cr || 0)),
                        backgroundColor: colors,
                        borderRadius: 6,
                        borderSkipped: false,
                    }],
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: (ctx) => ctx[0].label,
                                label: (ctx) => {
                                    const s = sorted[ctx.dataIndex];
                                    return [
                                        `State: ${s.state_name}`,
                                        `Funding: ${this.fmtINR(s.funding_inr_cr, 1)}`,
                                        `Deals: ${s.total_deals}`,
                                        `Investors: ${s.unique_investors}`,
                                        `Sector: ${s.dominant_sector}`,
                                    ];
                                },
                            },
                        },
                    },
                    scales: {
                        x: {
                            ticks: {
                                callback: (v) => `₹${v} Cr`,
                                font: { family: 'monospace', size: 10 },
                            },
                            grid: { color: 'rgba(148,163,184,0.15)' },
                        },
                        y: {
                            ticks: { font: { family: 'DM Sans', size: 11 } },
                            grid: { display: false },
                        },
                    },
                },
            });
        },

        buildSectorChart() {
            const canvas = document.getElementById('sectorChart');
            if (!canvas) {
                return;
            }

            if (this.charts.sector) {
                this.charts.sector.destroy();
            }

            const topStates = [...this.statesData]
                .sort((a, b) => Number(b.active_startups || 0) - Number(a.active_startups || 0))
                .slice(0, 8);

            this.charts.sector = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: topStates.map((s) => s.state_name),
                    datasets: [
                        {
                            label: 'Active Startups',
                            data: topStates.map((s) => Number(s.active_startups || 0)),
                            backgroundColor: topStates.map((s) => sectorColorFn(s.dominant_sector || '—') + 'cc'),
                            borderRadius: 4,
                        },
                        {
                            label: 'High Growth',
                            data: topStates.map((s) => Number(s.high_growth_startups || 0)),
                            backgroundColor: 'rgba(52,211,153,0.7)',
                            borderRadius: 4,
                        },
                        {
                            label: 'Women-led',
                            data: topStates.map((s) => Number(s.women_led_startups || 0)),
                            backgroundColor: 'rgba(167,139,250,0.7)',
                            borderRadius: 4,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: (ctx) => ctx[0].label,
                                label: (ctx) => {
                                    const s = topStates[ctx.dataIndex];
                                    return [
                                        `State: ${s.state_name}`,
                                        `Funding: ${this.fmtINR(s.funding_inr_cr, 1)}`,
                                        `Deals: ${s.total_deals}`,
                                        `Investors: ${s.unique_investors}`,
                                        `Sector: ${s.dominant_sector}`,
                                    ];
                                },
                            },
                        },
                    },
                    scales: {
                        x: {
                            ticks: { font: { family: 'DM Sans', size: 11 } },
                            grid: { display: false },
                        },
                        y: {
                            ticks: { font: { family: 'monospace', size: 10 } },
                            grid: { color: 'rgba(148,163,184,0.15)' },
                        },
                    },
                },
            });
        },

        median(arr) {
            const sorted = [...arr].sort((a, b) => a - b);
            const middle = Math.floor(sorted.length / 2);

            if (sorted.length === 0) {
                return 0;
            }

            return sorted.length % 2 !== 0
                ? sorted[middle]
                : (sorted[middle - 1] + sorted[middle]) / 2;
        },
    };
}
</script>
@endpush
