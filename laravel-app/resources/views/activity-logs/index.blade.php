@extends('layouts.app')

@php
    $title = 'Activity Logs';
    $pageTitle = 'Activity Logs';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Activity Logs', 'url' => route('activity-logs.index')],
    ];

    $removeFilter = static function (string $key) use ($filters, $sort, $perPage): string {
        $params = array_filter(
            array_merge($filters, ['sort' => $sort, 'per_page' => $perPage]),
            static fn ($value) => ! is_null($value) && $value !== ''
        );

        unset($params[$key]);

        return route('activity-logs.index', $params);
    };

    $metricCards = [
        [
            'label' => 'Total logs',
            'value' => number_format($totalToday),
            'icon' => 'activity',
            'accent' => 'cyan',
        ],
        [
            'label' => 'Success',
            'value' => number_format($successToday),
            'icon' => 'check-circle',
            'accent' => 'emerald',
        ],
        [
            'label' => 'Blocked',
            'value' => number_format($blockedToday),
            'icon' => 'shield',
            'accent' => 'yellow',
        ],
        [
            'label' => 'Failed',
            'value' => number_format($failedToday),
            'icon' => 'alert-circle',
            'accent' => 'indigo',
        ],
    ];
@endphp

@section('content')
<section class="space-y-6" x-data="activityTimeline()" x-init="init()" @keydown.escape.window="closeMetadata()">
    <div class="rounded-3xl border border-slate-800 bg-slate-900/90 px-6 py-6 shadow-[0_0_40px_rgba(34,211,238,0.08)] backdrop-blur-xl">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl space-y-3">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-cyan-300/80">Enterprise audit stream</p>
                <h1 class="text-3xl font-semibold tracking-tight text-white sm:text-4xl">Activity Logs</h1>
                <p class="text-sm leading-6 text-slate-400">Monitor user actions, system events, and audit trails in a fixed-height premium timeline designed for dense operational review.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <x-ui.button href="{{ route('activity-logs.export', array_merge($filters, ['sort' => $sort])) }}" variant="secondary">
                    <x-ui.icon name="download" class="h-4 w-4" />
                    Export log
                </x-ui.button>
                <x-ui.button type="button" @click="document.getElementById('filterForm').scrollIntoView({ behavior: 'smooth', block: 'center' })">
                    <x-ui.icon name="filter" class="h-4 w-4" />
                    Filters
                </x-ui.button>
            </div>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($metricCards as $card)
            @php
                $cardAccent = match ($card['accent']) {
                    'emerald' => ['border' => 'border-emerald-400/20', 'icon' => 'text-emerald-300', 'label' => 'text-emerald-200'],
                    'yellow' => ['border' => 'border-yellow-400/20', 'icon' => 'text-yellow-300', 'label' => 'text-yellow-100'],
                    'indigo' => ['border' => 'border-indigo-400/20', 'icon' => 'text-indigo-300', 'label' => 'text-indigo-100'],
                    default => ['border' => 'border-cyan-400/20', 'icon' => 'text-cyan-300', 'label' => 'text-cyan-100'],
                };
            @endphp

            <div class="rounded-3xl border {{ $cardAccent['border'] }} bg-slate-900/90 p-5 shadow-[0_0_40px_rgba(34,211,238,0.08)] backdrop-blur-xl">
                <div class="flex items-start justify-between gap-4">
                    <span class="text-[10px] font-semibold uppercase tracking-[0.28em] text-slate-500">{{ $card['label'] }}</span>
                    <x-ui.icon name="{{ $card['icon'] }}" class="h-5 w-5 {{ $cardAccent['icon'] }}" />
                </div>
                <div class="mt-4 text-3xl font-semibold leading-none {{ $cardAccent['label'] }}">{{ $card['value'] }}</div>
            </div>
        @endforeach
    </div>

    <div class="rounded-3xl border border-slate-800 bg-slate-900/90 p-6 shadow-[0_0_40px_rgba(34,211,238,0.08)] backdrop-blur-xl">
        <form method="GET" action="{{ route('activity-logs.index') }}" id="filterForm">
            <input type="hidden" name="sort" value="{{ $sort }}">

            <div class="grid gap-4 xl:grid-cols-12">
                <div class="xl:col-span-4">
                    <x-ui.form-field label="Search activity" help="Search users, actions, descriptions, or modules.">
                        <div class="relative">
                            <x-ui.icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-500" />
                            <input
                                type="search"
                                name="search"
                                value="{{ $filters['search'] ?? '' }}"
                                placeholder="Search logs..."
                                class="input-modern border-slate-700 bg-slate-950/60 pl-10 text-slate-100 placeholder:text-slate-500"
                                x-on:input.debounce.500ms="$el.closest('form').submit()"
                            >
                        </div>
                    </x-ui.form-field>
                </div>

                <div class="xl:col-span-2">
                    <x-ui.select-field label="Module">
                        <select name="module" class="select-modern border-slate-700 bg-slate-950/60 text-slate-100" onchange="this.form.submit()">
                            <option value="">All modules</option>
                            @foreach ($modules as $module => $icon)
                                <option value="{{ $module }}" @selected(($filters['module'] ?? '') === $module)>{{ $module }}</option>
                            @endforeach
                        </select>
                    </x-ui.select-field>
                </div>

                <div class="xl:col-span-2">
                    <x-ui.select-field label="Result">
                        <select name="result" class="select-modern border-slate-700 bg-slate-950/60 text-slate-100" onchange="this.form.submit()">
                            <option value="">All results</option>
                            @foreach ($results as $result)
                                <option value="{{ $result }}" @selected(($filters['result'] ?? '') === $result)>{{ $result }}</option>
                            @endforeach
                        </select>
                    </x-ui.select-field>
                </div>

                <div class="xl:col-span-2">
                    <x-ui.select-field label="Date range">
                        <select name="date_range" class="select-modern border-slate-700 bg-slate-950/60 text-slate-100" onchange="this.form.submit()">
                            @foreach ($dateRanges as $key => $label)
                                <option value="{{ $key }}" @selected(($filters['date_range'] ?? 'today') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </x-ui.select-field>
                </div>

                <div class="xl:col-span-2">
                    <x-ui.select-field label="User">
                        <select name="user_id" class="select-modern border-slate-700 bg-slate-950/60 text-slate-100" onchange="this.form.submit()">
                            <option value="">All users</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @selected((string) ($filters['user_id'] ?? '') === (string) $user->id)>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </x-ui.select-field>
                </div>

                <div class="xl:col-span-2 flex items-end gap-2">
                    <a href="{{ route('activity-logs.index') }}" class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-2xl border border-slate-700 bg-slate-950/60 px-4 text-sm font-medium text-slate-200 transition hover:border-cyan-400/30 hover:bg-slate-900">
                        <x-ui.icon name="x" class="h-4 w-4" />
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    @if ($hasActiveFilters)
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-medium text-slate-500">Active filters:</span>

            @if (! empty($filters['search']))
                <a href="{{ $removeFilter('search') }}" class="inline-flex items-center gap-1.5 rounded-full border border-slate-700 bg-slate-900/80 px-3 py-1 text-xs font-medium text-slate-300 transition hover:border-cyan-400/30 hover:text-white">
                    Search: {{ \Illuminate\Support\Str::limit($filters['search'], 20) }}
                    <span class="text-cyan-300">×</span>
                </a>
            @endif

            @if (! empty($filters['module']))
                <a href="{{ $removeFilter('module') }}" class="inline-flex items-center gap-1.5 rounded-full border border-slate-700 bg-slate-900/80 px-3 py-1 text-xs font-medium text-slate-300 transition hover:border-cyan-400/30 hover:text-white">
                    Module: {{ $filters['module'] }}
                    <span class="text-cyan-300">×</span>
                </a>
            @endif

            @if (! empty($filters['result']))
                <a href="{{ $removeFilter('result') }}" class="inline-flex items-center gap-1.5 rounded-full border border-slate-700 bg-slate-900/80 px-3 py-1 text-xs font-medium text-slate-300 transition hover:border-cyan-400/30 hover:text-white">
                    Result: {{ $filters['result'] }}
                    <span class="text-cyan-300">×</span>
                </a>
            @endif

            @if (! empty($filters['user_id']))
                @php $userName = $users->firstWhere('id', $filters['user_id'])?->name ?? 'Unknown'; @endphp
                <a href="{{ $removeFilter('user_id') }}" class="inline-flex items-center gap-1.5 rounded-full border border-slate-700 bg-slate-900/80 px-3 py-1 text-xs font-medium text-slate-300 transition hover:border-cyan-400/30 hover:text-white">
                    User: {{ $userName }}
                    <span class="text-cyan-300">×</span>
                </a>
            @endif

            @if (! empty($filters['date_range']) && $filters['date_range'] !== 'today')
                @php $rangeLabel = $dateRanges[$filters['date_range']] ?? $filters['date_range']; @endphp
                <a href="{{ $removeFilter('date_range') }}" class="inline-flex items-center gap-1.5 rounded-full border border-slate-700 bg-slate-900/80 px-3 py-1 text-xs font-medium text-slate-300 transition hover:border-cyan-400/30 hover:text-white">
                    {{ $rangeLabel }}
                    @if (($filters['date_range'] ?? null) === 'custom' && ! empty($filters['date_from']))
                        ({{ $filters['date_from'] }} – {{ $filters['date_to'] ?? 'now' }})
                    @endif
                    <span class="text-cyan-300">×</span>
                </a>
            @endif

            <a href="{{ route('activity-logs.index') }}" class="text-xs font-medium text-cyan-300 underline-offset-2 hover:text-cyan-200 hover:underline">Clear all</a>
        </div>
    @endif

    <div class="rounded-3xl border border-slate-800 bg-slate-900/90 shadow-[0_0_40px_rgba(34,211,238,0.08)] backdrop-blur-xl">
        <div class="flex flex-col gap-3 border-b border-slate-800 px-6 py-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-white">Activity timeline</h2>
                <p class="mt-1 text-sm text-slate-400">Showing {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }} of {{ number_format($logs->total()) }} entries</p>
            </div>

            <div class="flex items-center gap-2 text-xs font-medium text-slate-400">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-800 bg-slate-950/60 px-3 py-1.5">
                    <span class="h-2 w-2 rounded-full bg-cyan-400"></span>
                    Scrollable timeline
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-800 bg-slate-950/60 px-3 py-1.5">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    10 logs per page
                </span>
            </div>
        </div>

        <div class="max-h-[650px] overflow-y-auto activity-scrollbar px-4 py-4 sm:px-6">
            @forelse ($logs as $log)
                @php
                    $tone = $log->action_tone;
                @endphp

                <article class="group rounded-2xl border border-slate-800/80 bg-slate-950/70 p-4 transition hover:border-cyan-400/30 hover:bg-slate-900/90">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="flex min-w-0 flex-1 gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl {{ $tone['badge'] }} ring-1 {{ $tone['ring'] }}">
                                <x-ui.icon name="{{ $log->action_icon }}" class="h-5 w-5 {{ $tone['icon'] }}" />
                            </div>

                            <div class="min-w-0 flex-1 space-y-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="truncate text-base font-semibold text-white">{{ $log->action }}</h3>
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-[11px] font-medium {{ $tone['pill'] }}">{{ $log->result }}</span>
                                </div>

                                @if ($log->description)
                                    <p class="max-w-4xl text-sm leading-6 text-slate-400">{{ $log->description }}</p>
                                @endif

                                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                    <div class="rounded-2xl border border-slate-800 bg-slate-900/80 px-3 py-2.5">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-500">Actor</p>
                                        <p class="mt-1 truncate text-sm font-medium text-slate-200">{{ $log->actor_name }}</p>
                                    </div>

                                    <div class="rounded-2xl border border-slate-800 bg-slate-900/80 px-3 py-2.5">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-500">Target user</p>
                                        <p class="mt-1 truncate text-sm font-medium text-slate-200">{{ $log->target_name }}</p>
                                    </div>

                                    <div class="rounded-2xl border border-slate-800 bg-slate-900/80 px-3 py-2.5">
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-500">IP address</p>
                                        <p class="mt-1 truncate font-mono text-sm font-medium text-slate-200">{{ $log->ip_address ?? '—' }}</p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-400">
                                    <span class="inline-flex items-center rounded-full border border-slate-800 bg-slate-900/80 px-3 py-1.5 font-medium text-slate-300">
                                        <x-ui.icon name="{{ $modules[$log->module] ?? 'activity' }}" class="mr-1.5 h-3.5 w-3.5 text-cyan-300" />
                                        {{ $log->module }}
                                    </span>

                                    @if (! empty($log->metadata))
                                        <button
                                            type="button"
                                            @click="openMetadata(@js($log->metadata), @js($log->action), @js($log->actor_name))"
                                            class="inline-flex items-center rounded-full border border-slate-800 bg-slate-900/80 px-3 py-1.5 font-medium text-slate-300 transition hover:border-cyan-400/30 hover:text-white"
                                        >
                                            <x-ui.icon name="eye" class="mr-1.5 h-3.5 w-3.5 text-cyan-300" />
                                            View metadata
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex shrink-0 flex-col items-start gap-1 lg:min-w-[190px] lg:items-end lg:text-right">
                            <time class="font-mono text-sm font-semibold text-slate-100">{{ $log->timestamp_label }}</time>
                            <span class="font-mono text-xs text-slate-500">{{ $log->relative_time }}</span>
                        </div>
                    </div>
                </article>
            @empty
                <div class="flex min-h-[420px] items-center justify-center px-6 py-12 text-center">
                    <div class="max-w-sm space-y-4">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl border border-slate-800 bg-slate-950/70 text-cyan-300 ring-1 ring-cyan-400/20">
                            <x-ui.icon name="inbox" class="h-8 w-8" />
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-lg font-semibold text-white">No activity logs found</h3>
                            <p class="text-sm leading-6 text-slate-400">Try adjusting the filters or date range to reveal matching activity.</p>
                        </div>
                        <a href="{{ route('activity-logs.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-700 bg-slate-950/60 px-4 py-2 text-sm font-medium text-slate-200 transition hover:border-cyan-400/30 hover:bg-slate-900">Clear filters</a>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="border-t border-slate-800 px-6 py-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <p class="text-sm text-slate-400">
                    Showing <span class="font-medium text-slate-200">{{ number_format($logs->firstItem() ?? 0) }}</span>
                    to <span class="font-medium text-slate-200">{{ number_format($logs->lastItem() ?? 0) }}</span>
                    of <span class="font-medium text-slate-200">{{ number_format($logs->total()) }}</span>
                </p>

                <div class="flex items-center gap-3">
                    @if ($logs->previousPageUrl())
                        <a href="{{ $logs->previousPageUrl() }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-700 bg-slate-950/60 px-4 py-2 text-sm font-medium text-slate-200 transition hover:border-cyan-400/30 hover:bg-slate-900">
                            <x-ui.icon name="chevron-left" class="h-4 w-4" />
                            Previous
                        </a>
                    @else
                        <span class="inline-flex items-center gap-2 rounded-2xl border border-slate-800 bg-slate-950/40 px-4 py-2 text-sm font-medium text-slate-500">
                            <x-ui.icon name="chevron-left" class="h-4 w-4" />
                            Previous
                        </span>
                    @endif

                    <span class="rounded-2xl border border-slate-800 bg-slate-950/60 px-4 py-2 text-sm font-medium text-slate-300">
                        Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}
                    </span>

                    @if ($logs->nextPageUrl())
                        <a href="{{ $logs->nextPageUrl() }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-700 bg-slate-950/60 px-4 py-2 text-sm font-medium text-slate-200 transition hover:border-cyan-400/30 hover:bg-slate-900">
                            Next
                            <x-ui.icon name="chevron-right" class="h-4 w-4" />
                        </a>
                    @else
                        <span class="inline-flex items-center gap-2 rounded-2xl border border-slate-800 bg-slate-950/40 px-4 py-2 text-sm font-medium text-slate-500">
                            Next
                            <x-ui.icon name="chevron-right" class="h-4 w-4" />
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div x-cloak x-show="metadataModal.open" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm" @click.self="closeMetadata()">
        <div class="w-full max-w-2xl rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-[0_0_40px_rgba(34,211,238,0.08)]">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-300/80">Metadata</p>
                    <h3 class="mt-2 text-xl font-semibold text-white" x-text="metadataModal.action"></h3>
                    <p class="mt-1 text-sm text-slate-400">Actor: <span class="text-slate-200" x-text="metadataModal.actor"></span></p>
                </div>
                <button type="button" @click="closeMetadata()" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-800 bg-slate-950/60 text-slate-300 transition hover:border-cyan-400/30 hover:text-white">
                    <x-ui.icon name="x" class="h-5 w-5" />
                </button>
            </div>

            <pre class="mt-6 max-h-96 overflow-auto rounded-2xl border border-slate-800 bg-slate-950/70 p-4 text-xs leading-6 text-slate-300 activity-scrollbar" x-text="metadataModal.data ? JSON.stringify(metadataModal.data, null, 2) : '{}' "></pre>

            <div class="mt-6 flex justify-end">
                <button type="button" @click="closeMetadata()" class="inline-flex items-center justify-center rounded-2xl border border-slate-700 bg-slate-950/60 px-4 py-2 text-sm font-medium text-slate-200 transition hover:border-cyan-400/30 hover:bg-slate-900">Close</button>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-2xl border border-emerald-400/20 bg-emerald-500/15 px-5 py-3 text-emerald-50 shadow-xl backdrop-blur">
            <x-ui.icon name="check-circle" class="h-5 w-5 text-emerald-300" />
            {{ session('success') }}
        </div>
    @endif
</section>
@endsection

@push('scripts')
    <script>
function activityTimeline() {
    return {
        metadataModal: {
            open: false,
            data: null,
            action: '',
            actor: '',
        },

        init() {},

        openMetadata(data, action, actor) {
            this.metadataModal = {
                open: true,
                data,
                action,
                actor,
            };
        },

        closeMetadata() {
            this.metadataModal.open = false;
        },
    };
}
</script>
@endpush