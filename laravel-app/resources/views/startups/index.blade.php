@extends('layouts.app')

@php
    $title = 'Startups';
    $pageTitle = 'Startups';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Startups', 'url' => route('startups.index')],
    ];

    $startupIds = $startups->pluck('id')->map(fn ($id) => (string) $id)->values()->all();
    $showingFrom = $startups->firstItem() ?? 0;
    $showingTo = $startups->lastItem() ?? 0;

    $buildIndexUrl = function (array $overrides = [], array $remove = []) use ($filters, $sort, $perPage) {
        $params = array_merge($filters, $overrides);

        foreach ($remove as $key) {
            unset($params[$key]);
        }

        $params['sort'] = $overrides['sort'] ?? $sort;
        $params['per_page'] = $overrides['per_page'] ?? $perPage;

        $params = array_filter($params, function ($value): bool {
            return ! is_null($value) && $value !== '' && $value !== [];
        });

        return route('startups.index', $params);
    };

    $sortIndicator = function (array $keys) use ($sort) {
        if (! in_array($sort, $keys, true)) {
            return '⇅';
        }

        return $sort === $keys[0] ? '↑' : '↓';
    };

    $sortActive = function (array $keys) use ($sort) {
        return in_array($sort, $keys, true);
    };

    $activeFilterCount = collect($filters)->filter(function ($value): bool {
        return ! is_null($value) && $value !== '' && $value !== [];
    })->count();

    $startupStages = $fundingStages->values();
@endphp

@section('content')
<section class="space-y-6" x-data="startupAdmin(@js($startupIds))" x-init="init()" x-effect="selectAll = startupIds.length > 0 && selectedIds.length === startupIds.length" @keydown.escape.window="closeActionMenu()">
    <x-ui.section-header
        title="Startup management"
        subtitle="Search, filter, sort, export, and manage startup records in a data-rich admin interface."
    >
        <div class="flex flex-wrap items-center gap-3">
            <x-ui.button href="{{ route('startups.export', array_merge($filters, ['sort' => $sort])) }}" variant="secondary">
                <x-ui.icon name="download" class="h-4 w-4" />
                Export CSV
            </x-ui.button>

            <x-ui.button href="{{ route('startups.create') }}">
                <x-ui.icon name="plus" class="h-4 w-4" />
                Add Startup
            </x-ui.button>
        </div>
    </x-ui.section-header>

    <div class="flex flex-wrap gap-3">
        <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200">
            <x-ui.icon name="grid" class="h-4 w-4 text-indigo-600 dark:text-indigo-400" />
            <span>{{ number_format($filteredTotal) }}</span>
            <span class="font-medium text-slate-500 dark:text-slate-400">startups found</span>
        </div>

        <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200">
            <x-ui.icon name="check-circle" class="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
            <span>{{ number_format($activeCount) }}</span>
            <span class="font-medium text-slate-500 dark:text-slate-400">active</span>
        </div>

        <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200">
            <x-ui.icon name="shield" class="h-4 w-4 text-sky-600 dark:text-sky-400" />
            <span>{{ number_format($dpiitCount) }}</span>
            <span class="font-medium text-slate-500 dark:text-slate-400">DPIIT</span>
        </div>

        <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200">
            <x-ui.icon name="users" class="h-4 w-4 text-violet-600 dark:text-violet-400" />
            <span>{{ number_format($womenLedCount) }}</span>
            <span class="font-medium text-slate-500 dark:text-slate-400">women-led</span>
        </div>
    </div>
<x-ui.card>
    <form method="GET" action="{{ route('startups.index') }}" id="filterForm">
        <input type="hidden" name="per_page" value="{{ $perPage }}">

        <div class="grid gap-4 items-end xl:grid-cols-12">

            <!-- Search -->
            <div class="xl:col-span-4 min-w-0">
                <x-ui.form-field label="Advanced search">
                    <p class="text-xs leading-5 text-slate-500 dark:text-slate-400">Search by startup name, founder, sector, or registration code.</p>
                    <input
                        type="search"
                        name="search"
                        value="{{ $filters['search'] ?? '' }}"
                        placeholder="Search startups..."
                        class="input-modern w-full"
                        x-on:input.debounce.400ms="$el.form.submit()"
                    />
                </x-ui.form-field>
            </div>

            <!-- Sector -->
            <div class="xl:col-span-2 min-w-0">
                <x-ui.select-field label="Sector">
                    <select
                        name="sector_id"
                        class="select-modern w-full min-w-0 truncate"
                        onchange="this.form.submit()"
                    >
                        <option value="">All sectors</option>

                        @foreach ($sectors as $sector)
                            <option
                                value="{{ $sector->id }}"
                                @selected((string) ($filters['sector_id'] ?? '') === (string) $sector->id)
                            >
                                {{ $sector->sector_name }}
                            </option>
                        @endforeach
                    </select>
                </x-ui.select-field>
            </div>

            <!-- State -->
            <div class="xl:col-span-2 min-w-0">
                <x-ui.select-field label="State">
                    <select
                        name="state_id"
                        class="select-modern w-full min-w-0 truncate"
                        onchange="this.form.submit()"
                    >
                        <option value="">All states</option>

                        @foreach ($states as $state)
                            <option
                                value="{{ $state->id }}"
                                @selected((string) ($filters['state_id'] ?? '') === (string) $state->id)
                            >
                                {{ $state->state_name }}
                            </option>
                        @endforeach
                    </select>
                </x-ui.select-field>
            </div>

            <!-- Sort -->
            <div class="xl:col-span-2 min-w-0">
                <x-ui.select-field label="Sort by">
                    <select
                        name="sort"
                        class="select-modern w-full min-w-0 truncate"
                        onchange="this.form.submit()"
                    >
                        <option value="newest" @selected($sort === 'newest')>
                            Newest first
                        </option>

                        <option value="oldest" @selected($sort === 'oldest')>
                            Oldest first
                        </option>

                        <option value="funding_high" @selected($sort === 'funding_high')>
                            Funding highest
                        </option>

                        <option value="funding_low" @selected($sort === 'funding_low')>
                            Funding lowest
                        </option>

                        <option value="growth_high" @selected($sort === 'growth_high')>
                            Growth rate ↑
                        </option>

                        <option value="growth_low" @selected($sort === 'growth_low')>
                            Growth rate ↓
                        </option>

                        <option value="name_asc" @selected($sort === 'name_asc')>
                            Name A–Z
                        </option>

                        <option value="name_desc" @selected($sort === 'name_desc')>
                            Name Z–A
                        </option>

                        <option value="employees_high" @selected($sort === 'employees_high')>
                            Employees highest
                        </option>

                        <option value="valuation_high" @selected($sort === 'valuation_high')>
                            Valuation highest
                        </option>
                    </select>
                </x-ui.select-field>
            </div>

            <!-- Filters Button -->
            <div class="xl:col-span-2 min-w-0">
                <div class="flex items-end gap-2">

                    <x-ui.button
                        type="button"
                        variant="secondary"
                        class="relative w-full justify-center whitespace-nowrap"
                        @click="drawerOpen = true"
                    >
                        <x-ui.icon name="filter" class="h-4 w-4 shrink-0" />

                        <span class="truncate">
                            Filters
                        </span>

                        @if ($activeFilterCount > 0)
                            <span class="absolute -right-1.5 -top-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-indigo-600 text-[11px] font-semibold text-white">
                                {{ $activeFilterCount }}
                            </span>
                        @endif
                    </x-ui.button>

                    @if ($activeFilterCount > 0)
                        <x-ui.button
                            href="{{ route('startups.index') }}"
                            variant="secondary"
                            title="Clear all filters"
                            class="shrink-0"
                        >
                            <x-ui.icon name="x" class="h-4 w-4" />
                        </x-ui.button>
                    @endif

                </div>
            </div>

        </div>
    </form>
</x-ui.card>
    @if ($activeFilterCount > 0)
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Active filters:</span>

            @if (! empty($filters['search']))
                <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                    Search: {{ $filters['search'] }}
                    <a class="ml-1 opacity-60 transition hover:opacity-100 hover:text-rose-500" href="{{ $buildIndexUrl([], ['search']) }}">×</a>
                </span>
            @endif

            @if (! empty($filters['sector_id']))
                @php $sectorName = $sectors->firstWhere('id', $filters['sector_id'])?->sector_name; @endphp
                <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                    Sector: {{ $sectorName }}
                    <a class="ml-1 opacity-60 transition hover:opacity-100 hover:text-rose-500" href="{{ $buildIndexUrl([], ['sector_id']) }}">×</a>
                </span>
            @endif

            @if (! empty($filters['state_id']))
                @php $stateName = $states->firstWhere('id', $filters['state_id'])?->state_name; @endphp
                <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                    State: {{ $stateName }}
                    <a class="ml-1 opacity-60 transition hover:opacity-100 hover:text-rose-500" href="{{ $buildIndexUrl([], ['state_id']) }}">×</a>
                </span>
            @endif

            @if (! empty($filters['funding_stage']))
                <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                    Stage: {{ implode(', ', (array) $filters['funding_stage']) }}
                    <a class="ml-1 opacity-60 transition hover:opacity-100 hover:text-rose-500" href="{{ $buildIndexUrl([], ['funding_stage']) }}">×</a>
                </span>
            @endif

            @if (! empty($filters['status']))
                <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                    Status: {{ $filters['status'] }}
                    <a class="ml-1 opacity-60 transition hover:opacity-100 hover:text-rose-500" href="{{ $buildIndexUrl([], ['status']) }}">×</a>
                </span>
            @endif

            @if (! empty($filters['dpiit_recognized']))
                <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                    DPIIT: {{ $filters['dpiit_recognized'] }}
                    <a class="ml-1 opacity-60 transition hover:opacity-100 hover:text-rose-500" href="{{ $buildIndexUrl([], ['dpiit_recognized']) }}">×</a>
                </span>
            @endif

            @if (! empty($filters['women_led']))
                <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                    Women-led only
                    <a class="ml-1 opacity-60 transition hover:opacity-100 hover:text-rose-500" href="{{ $buildIndexUrl([], ['women_led']) }}">×</a>
                </span>
            @endif

            @if (! empty($filters['ai_enabled']))
                <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                    AI-enabled only
                    <a class="ml-1 opacity-60 transition hover:opacity-100 hover:text-rose-500" href="{{ $buildIndexUrl([], ['ai_enabled']) }}">×</a>
                </span>
            @endif

            @if (! empty($filters['year_from']) || ! empty($filters['year_to']))
                <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                    Founded: {{ $filters['year_from'] ?? 'Any' }} – {{ $filters['year_to'] ?? 'Any' }}
                    <a class="ml-1 opacity-60 transition hover:opacity-100 hover:text-rose-500" href="{{ $buildIndexUrl([], ['year_from', 'year_to']) }}">×</a>
                </span>
            @endif

            @if (! empty($filters['funding_min']) || ! empty($filters['funding_max']))
                <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                    Funding: {{ $filters['funding_min'] ?? 'Any' }} – {{ $filters['funding_max'] ?? 'Any' }}
                    <a class="ml-1 opacity-60 transition hover:opacity-100 hover:text-rose-500" href="{{ $buildIndexUrl([], ['funding_min', 'funding_max']) }}">×</a>
                </span>
            @endif

            <a href="{{ route('startups.index') }}" class="text-xs font-medium text-rose-500 underline underline-offset-2 hover:text-rose-600">
                Clear all
            </a>
        </div>
    @endif

    <div x-cloak x-show="selectedIds.length > 0" x-transition class="flex items-center gap-3 rounded-2xl border border-indigo-200 bg-indigo-50 px-5 py-3 dark:border-indigo-900 dark:bg-indigo-950/40">
        <span class="text-sm font-semibold text-indigo-700 dark:text-indigo-300">
            <span x-text="selectedIds.length"></span>
            startups selected
        </span>

        <div class="ml-auto flex flex-wrap items-center gap-2">
            <x-ui.button type="button" variant="secondary" @click="bulkExport()">
                <x-ui.icon name="download" class="h-4 w-4" />
                Export Selected
            </x-ui.button>

            <select class="select-modern w-auto min-w-44" @change="bulkStatus($event.target.value)">
                <option value="">Change status...</option>
                @foreach ($statuses as $statusOption)
                    <option value="{{ $statusOption }}">{{ $statusOption }}</option>
                @endforeach
            </select>

            <x-ui.button type="button" variant="danger" @click="confirmBulkDelete()">
                <x-ui.icon name="trash" class="h-4 w-4" />
                Delete Selected
            </x-ui.button>

            <x-ui.button type="button" variant="ghost" @click="selectedIds = []; selectAll = false">
                <x-ui.icon name="x" class="h-4 w-4" />
                Deselect all
            </x-ui.button>
        </div>
    </div>

    <x-ui.card padding="p-0">
        <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-800">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Startup registry</h3>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Showing {{ $showingFrom }}–{{ $showingTo }} of {{ number_format($startups->total()) }} startups
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                <thead class="bg-slate-50/80 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:bg-slate-900/80 dark:text-slate-400">
                    <tr>
                        <th class="w-12 px-6 py-4">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="checkbox-modern" />
                        </th>
                        <th class="px-6 py-4">
                            <a class="inline-flex items-center gap-1 {{ $sortActive(['name_asc', 'name_desc']) ? 'font-bold text-indigo-600 dark:text-indigo-400' : 'hover:text-indigo-600 dark:hover:text-indigo-400' }}" href="{{ $buildIndexUrl(['sort' => $sort === 'name_asc' ? 'name_desc' : 'name_asc']) }}">
                                Startup Name <span class="text-xs opacity-60">{{ $sortIndicator(['name_asc', 'name_desc']) }}</span>
                            </a>
                        </th>
                        <th class="px-6 py-4">Sector</th>
                        <th class="px-6 py-4">State</th>
                        <th class="px-6 py-4">
                            <a class="inline-flex items-center gap-1 {{ $sortActive(['founded_new', 'founded_old']) ? 'font-bold text-indigo-600 dark:text-indigo-400' : 'hover:text-indigo-600 dark:hover:text-indigo-400' }}" href="{{ $buildIndexUrl(['sort' => $sort === 'founded_new' ? 'founded_old' : 'founded_new']) }}">
                                Founded <span class="text-xs opacity-60">{{ $sortIndicator(['founded_new', 'founded_old']) }}</span>
                            </a>
                        </th>
                        <th class="px-6 py-4">
                            <a class="inline-flex items-center gap-1 {{ $sortActive(['funding_high', 'funding_low']) ? 'font-bold text-indigo-600 dark:text-indigo-400' : 'hover:text-indigo-600 dark:hover:text-indigo-400' }}" href="{{ $buildIndexUrl(['sort' => $sort === 'funding_high' ? 'funding_low' : 'funding_high']) }}">
                                Funding (Rs.) <span class="text-xs opacity-60">{{ $sortIndicator(['funding_high', 'funding_low']) }}</span>
                            </a>
                        </th>
                        <th class="px-6 py-4">
                            <a class="inline-flex items-center gap-1 {{ $sortActive(['growth_high', 'growth_low']) ? 'font-bold text-indigo-600 dark:text-indigo-400' : 'hover:text-indigo-600 dark:hover:text-indigo-400' }}" href="{{ $buildIndexUrl(['sort' => $sort === 'growth_high' ? 'growth_low' : 'growth_high']) }}">
                                Growth % <span class="text-xs opacity-60">{{ $sortIndicator(['growth_high', 'growth_low']) }}</span>
                            </a>
                        </th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($startups as $startup)
                        @php
                            $fundingValue = (float) ($startup->total_funding_usd ?? 0);
                            $formattedFunding = match (true) {
                                $fundingValue >= 1e9 => 'Rs.'.number_format($fundingValue / 1e9, 2).'B',
                                $fundingValue >= 1e6 => 'Rs.'.number_format($fundingValue / 1e6, 1).'M',
                                $fundingValue >= 1e3 => 'Rs.'.number_format($fundingValue / 1e3, 0).'K',
                                $fundingValue == 0.0 => 'Bootstrapped',
                                default => 'Rs.'.number_format($fundingValue, 2),
                            };

                            $growthValue = (float) ($startup->growth_percentage ?? 0);
                        @endphp

                        <tr class="group hover:bg-slate-50/70 dark:hover:bg-slate-800/40" :class="{ 'bg-indigo-50/50 dark:bg-indigo-950/20': selectedIds.includes('{{ $startup->id }}') }">
                            <td class="px-6 py-4">
                                <input type="checkbox" value="{{ $startup->id }}" x-model="selectedIds" class="checkbox-modern" />
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-slate-900 dark:text-white">{{ $startup->startup_name }}</span>
                                    <span class="mt-0.5 text-xs text-slate-400 dark:text-slate-500">{{ $startup->registration_number ?: '—' }} · {{ $startup->city ?: '—' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">{{ $startup->sector?->sector_name ?? '—' }}</td>
                            <td class="px-6 py-4">{{ $startup->state?->state_name ?? '—' }}</td>
                            <td class="px-6 py-4">
                                {{ $startup->founded_year ?? '—' }}
                            </td>
                            <td class="px-6 py-4 font-mono text-sm text-slate-700 dark:text-slate-200">{{ $formattedFunding }}</td>
                            <td class="px-6 py-4 font-mono text-sm {{ $growthValue > 0 ? 'text-emerald-600 dark:text-emerald-400' : ($growthValue < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400 dark:text-slate-500') }}">
                                @if ($growthValue > 0)
                                    +{{ rtrim(rtrim(number_format($growthValue, 2), '0'), '.') }}%
                                @elseif ($growthValue < 0)
                                    {{ rtrim(rtrim(number_format($growthValue, 2), '0'), '.') }}%
                                @else
                                    0%
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="relative inline-flex">
                                    <button type="button" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800" @click.stop="toggleActionMenu('{{ $startup->id }}')" :aria-expanded="(openActionMenu === '{{ $startup->id }}').toString()">
                                        Actions
                                        <span class="transition-transform duration-200" :class="openActionMenu === '{{ $startup->id }}' ? 'rotate-180' : ''">
                                            <x-ui.icon name="chevron-down" class="h-4 w-4" />
                                        </span>
                                    </button>
                                    <div x-cloak x-show="openActionMenu === '{{ $startup->id }}'" x-transition.origin.top.right @click.outside="closeActionMenu()" class="absolute right-0 top-12 z-20 w-48 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl shadow-slate-200/70 dark:border-slate-800 dark:bg-slate-950 dark:shadow-slate-950/40">
                                        <a href="{{ route('startups.show', ['startup' => $startup->id]) }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                                            <x-ui.icon name="eye" class="h-4 w-4" /> View
                                        </a>
                                        <a href="{{ route('startups.edit', $startup) }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                                            <x-ui.icon name="edit" class="h-4 w-4" /> Edit
                                        </a>
                                        <button type="button" class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-sm text-rose-600 transition hover:bg-rose-500/10 dark:text-rose-400" @click="confirmDelete('{{ $startup->id }}', @js($startup->startup_name))">
                                            <x-ui.icon name="trash" class="h-4 w-4" /> Delete
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="px-6 py-16">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-indigo-600/10 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                                        <x-ui.icon name="inbox" class="h-8 w-8" />
                                    </div>
                                    <p class="text-base font-semibold text-slate-700 dark:text-white">No startups found</p>
                                    <p class="text-sm text-slate-400">Try adjusting your search or clearing filters.</p>
                                    <x-ui.button href="{{ route('startups.index') }}" variant="secondary">Clear filters</x-ui.button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-4 border-t border-slate-200 px-6 py-4 dark:border-slate-800 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                Show
                <select name="per_page" onchange="updatePerPage(this.value)" class="select-modern w-auto">
                    @foreach ([10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" @selected((int) $perPage === $size)>{{ $size }}</option>
                    @endforeach
                </select>
                per page
            </div>

            <span class="text-sm text-slate-500 dark:text-slate-400">
                Showing <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $showingFrom }}</span>
                to <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $showingTo }}</span>
                of <span class="font-semibold text-slate-700 dark:text-slate-200">{{ number_format($startups->total()) }}</span> startups
            </span>

            <div>
                {{ $startups->links() }}
            </div>
        </div>
    </x-ui.card>

    <div x-cloak x-show="drawerOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 z-50 flex justify-end">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="drawerOpen = false"></div>

        <div x-show="drawerOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" class="relative z-10 flex h-full w-96 flex-col overflow-hidden border-l border-slate-200 bg-white shadow-2xl dark:border-slate-800 dark:bg-slate-950">
            <div class="relative flex items-center border-b border-slate-200 px-6 py-6 pr-14 dark:border-slate-800">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Advanced Filters</h3>
                <button type="button" class="absolute right-4 top-4 inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-600 transition hover:border-slate-300 hover:text-slate-900 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800" @click="drawerOpen = false" aria-label="Close filters panel">
                    <x-ui.icon name="x" class="h-5 w-5" />
                </button>
            </div>

            <form id="advancedFilterForm" method="GET" action="{{ route('startups.index') }}" class="flex min-h-0 flex-1 flex-col overflow-hidden">
                <div class="min-h-0 flex-1 space-y-6 overflow-y-auto p-6">
                    <input type="hidden" name="search" value="{{ $filters['search'] ?? '' }}">
                    <input type="hidden" name="sector_id" value="{{ $filters['sector_id'] ?? '' }}">
                    <input type="hidden" name="state_id" value="{{ $filters['state_id'] ?? '' }}">
                    <input type="hidden" name="sort" value="{{ $sort }}">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">

                    <div>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">Funding Stage</p>
                        <div class="mt-3 space-y-2">
                            @foreach ($startupStages as $stage)
                                <label class="flex cursor-pointer items-center gap-2.5">
                                    <input type="checkbox" name="funding_stage[]" value="{{ $stage }}" class="checkbox-modern" @checked(in_array($stage, (array) ($filters['funding_stage'] ?? []), true))>
                                    <span class="text-sm text-slate-700 dark:text-slate-300">{{ $stage }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">Status</p>
                        <div class="mt-3 space-y-2">
                            <label class="flex cursor-pointer items-center gap-2.5">
                                <input type="radio" name="status" value="" class="checkbox-modern" @checked(empty($filters['status']))>
                                <span class="text-sm text-slate-700 dark:text-slate-300">All</span>
                            </label>
                            @foreach ($statuses as $statusOption)
                                <label class="flex cursor-pointer items-center gap-2.5">
                                    <input type="radio" name="status" value="{{ $statusOption }}" class="checkbox-modern" @checked(($filters['status'] ?? '') === $statusOption)>
                                    <span class="text-sm text-slate-700 dark:text-slate-300">{{ $statusOption }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">DPIIT Recognized</p>
                        <div class="mt-3 space-y-2">
                            <label class="flex cursor-pointer items-center gap-2.5">
                                <input type="radio" name="dpiit_recognized" value="" class="checkbox-modern" @checked(empty($filters['dpiit_recognized']))>
                                <span class="text-sm text-slate-700 dark:text-slate-300">All</span>
                            </label>
                            <label class="flex cursor-pointer items-center gap-2.5">
                                <input type="radio" name="dpiit_recognized" value="Yes" class="checkbox-modern" @checked(($filters['dpiit_recognized'] ?? '') === 'Yes')>
                                <span class="text-sm text-slate-700 dark:text-slate-300">Yes</span>
                            </label>
                            <label class="flex cursor-pointer items-center gap-2.5">
                                <input type="radio" name="dpiit_recognized" value="No" class="checkbox-modern" @checked(($filters['dpiit_recognized'] ?? '') === 'No')>
                                <span class="text-sm text-slate-700 dark:text-slate-300">No</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">Women-led Startups</p>
                        <label class="mt-3 flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-800 dark:bg-slate-900">
                            <span class="text-sm text-slate-700 dark:text-slate-300">Enable filter</span>
                            <input type="checkbox" name="women_led" value="1" class="checkbox-modern" @checked(! empty($filters['women_led']))>
                        </label>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">AI-enabled Startups</p>
                        <label class="mt-3 flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-800 dark:bg-slate-900">
                            <span class="text-sm text-slate-700 dark:text-slate-300">Enable filter</span>
                            <input type="checkbox" name="ai_enabled" value="1" class="checkbox-modern" @checked(! empty($filters['ai_enabled']))>
                        </label>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">Founded Year</p>
                        <div class="mt-3 grid grid-cols-2 gap-3">
                            <input type="number" name="year_from" min="2010" max="2024" value="{{ $filters['year_from'] ?? '' }}" placeholder="From" class="input-modern">
                            <input type="number" name="year_to" min="2010" max="2024" value="{{ $filters['year_to'] ?? '' }}" placeholder="To" class="input-modern">
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">Total Funding (Rs.)</p>
                        <div class="mt-3 grid grid-cols-2 gap-3">
                            <input type="number" name="funding_min" value="{{ $filters['funding_min'] ?? '' }}" placeholder="Min" class="input-modern">
                            <input type="number" name="funding_max" value="{{ $filters['funding_max'] ?? '' }}" placeholder="Max" class="input-modern">
                        </div>
                    </div>
                </div>

                <div class="shrink-0 border-t border-slate-200 p-6 dark:border-slate-800">
                    <div class="flex gap-3">
                        <a href="{{ route('startups.index') }}" class="inline-flex flex-1 items-center justify-center rounded-2xl bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                            Clear All
                        </a>
                        <x-ui.button type="submit" form="advancedFilterForm" class="flex-1 justify-center">
                            Apply Filters
                        </x-ui.button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div x-cloak x-show="deleteModal.open" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" @keydown.escape.window="deleteModal.open = false">
        <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400">
                <x-ui.icon name="trash" class="h-6 w-6" />
            </div>

            <h3 class="mt-4 font-semibold text-slate-900 dark:text-white">Delete startup?</h3>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                Are you sure you want to delete <strong x-text="deleteModal.name"></strong>? This action cannot be undone.
            </p>

            <div class="mt-6 flex gap-3">
                <x-ui.button type="button" variant="secondary" class="flex-1 justify-center" @click="deleteModal.open = false">Cancel</x-ui.button>
                <x-ui.button type="button" variant="danger" class="flex-1 justify-center" @click="executeDelete()">Delete</x-ui.button>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-2xl bg-green-600 px-5 py-3 text-white shadow-xl">
            <x-ui.icon name="check-circle" class="h-5 w-5" />
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-2xl bg-rose-600 px-5 py-3 text-white shadow-xl">
            <x-ui.icon name="x" class="h-5 w-5" />
            {{ session('error') }}
        </div>
    @endif

    @push('scripts')
        <script>
            function startupAdmin(startupIds = []) {
                return {
                    startupIds,
                    selectedIds: [],
                    selectAll: false,
                    drawerOpen: false,
                    openActionMenu: null,
                    deleteModal: {
                        open: false,
                        id: null,
                        name: '',
                    },

                    init() {
                        // Server-driven page; no client boot data required.
                    },

                    toggleAll() {
                        if (this.selectAll) {
                            this.selectedIds = [...this.startupIds];
                            return;
                        }

                        this.selectedIds = [];
                    },

                    toggleActionMenu(startupId) {
                        this.openActionMenu = this.openActionMenu === startupId ? null : startupId;
                    },

                    closeActionMenu() {
                        this.openActionMenu = null;
                    },

                    confirmDelete(id, name) {
                        this.closeActionMenu();
                        this.deleteModal = { open: true, id, name };
                    },

                    async executeDelete() {
                        const res = await fetch(`/startups/${this.deleteModal.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                            },
                        });

                        if (res.ok) {
                            window.location.reload();
                            return;
                        }

                        alert('Failed to delete startup.');
                    },

                    async bulkExport() {
                        const res = await fetch('/api/startups/bulk-export', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'text/csv',
                            },
                            body: JSON.stringify({ ids: this.selectedIds }),
                        });

                        if (!res.ok) {
                            alert('Failed to export selected startups.');
                            return;
                        }

                        const blob = await res.blob();
                        const url = window.URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.href = url;
                        link.download = 'startups_selected_export.csv';
                        document.body.appendChild(link);
                        link.click();
                        link.remove();
                        window.URL.revokeObjectURL(url);
                    },

                    async bulkStatus(status) {
                        if (!status) {
                            return;
                        }

                        const res = await fetch('/api/startups/bulk-status', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ ids: this.selectedIds, status }),
                        });

                        if (res.ok) {
                            window.location.reload();
                            return;
                        }

                        alert('Failed to update startup status.');
                    },

                    async confirmBulkDelete() {
                        const count = this.selectedIds.length;

                        if (!confirm(`Delete ${count} startups? This cannot be undone.`)) {
                            return;
                        }

                        const res = await fetch('/api/startups/bulk-delete', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ ids: this.selectedIds }),
                        });

                        if (res.ok) {
                            window.location.reload();
                            return;
                        }

                        alert('Failed to delete selected startups.');
                    },
                };
            }

            function updatePerPage(value) {
                const params = new URLSearchParams(window.location.search);
                params.set('per_page', value);
                params.set('page', 1);
                window.location.search = params.toString();
            }
        </script>
    @endpush
</section>
@endsection