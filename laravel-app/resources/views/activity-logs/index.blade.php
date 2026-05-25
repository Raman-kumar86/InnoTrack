@extends('layouts.app')

@php
    $title = 'Activity Logs';
    $pageTitle = 'Activity Logs';
@endphp

@section('content')
<section class="space-y-6" x-data="activityLogs()" x-init="init()">
    <x-ui.section-header
        title="Activity log"
        subtitle="Track audit trails, user actions, and system events with a structured timeline and filter panel."
    >
        <x-ui.button href="{{ route('activity-logs.export', array_merge($filters, ['sort' => $sort, 'per_page' => $perPage])) }}" variant="secondary">
            <x-ui.icon name="download" class="h-4 w-4" />
            Export log
        </x-ui.button>
        <x-ui.button type="button" onclick="document.getElementById('filterForm').submit()">
            <x-ui.icon name="filter" class="h-4 w-4" />
            Apply filter
        </x-ui.button>
    </x-ui.section-header>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $kpis = [
                ['label' => 'Total logs', 'value' => number_format($totalToday), 'icon' => 'activity', 'border' => 'border-slate-400/30', 'color' => 'text-slate-900 dark:text-white'],
                ['label' => 'Success', 'value' => number_format($successToday), 'icon' => 'check-circle', 'border' => 'border-green-500/40', 'color' => 'text-green-600 dark:text-green-400'],
                ['label' => 'Blocked', 'value' => number_format($blockedToday), 'icon' => 'shield', 'border' => 'border-amber-500/40', 'color' => 'text-amber-600 dark:text-amber-400'],
                ['label' => 'Failed', 'value' => number_format($failedToday), 'icon' => 'alert-circle', 'border' => 'border-rose-500/40', 'color' => 'text-rose-600 dark:text-rose-400'],
            ];
        @endphp

        @foreach ($kpis as $kpi)
            <x-ui.card class="border-l-4 {{ $kpi['border'] }}">
                <div class="mb-3 flex items-start justify-between">
                    <span class="text-[10px] font-mono uppercase tracking-widest text-slate-400 dark:text-slate-500">{{ $kpi['label'] }}</span>
                    <x-ui.icon name="{{ $kpi['icon'] }}" class="h-4 w-4 text-slate-300 dark:text-slate-600" />
                </div>
                <div class="text-3xl font-bold leading-none {{ $kpi['color'] }}">{{ $kpi['value'] }}</div>
            </x-ui.card>
        @endforeach
    </div>

    <x-ui.card>
        <form method="GET" action="{{ route('activity-logs.index') }}" id="filterForm">
            <input type="hidden" name="sort" value="{{ $sort }}" />
            <input type="hidden" name="per_page" value="{{ $perPage }}" />

            <div class="grid gap-4 xl:grid-cols-12">
                <div class="xl:col-span-4">
                    <x-ui.form-field label="Search activity" help="Search users or actions">
                        <div class="relative">
                            <x-ui.icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                            <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search users or actions..." class="input-modern pl-10" x-on:input.debounce.500ms="$el.closest('form').submit()" />
                        </div>
                    </x-ui.form-field>
                </div>

                <div class="xl:col-span-2">
                    <x-ui.select-field label="Module">
                        <select name="module" class="select-modern" onchange="this.form.submit()">
                            <option value="">All modules</option>
                            @foreach ($modules as $module => $icon)
                                <option value="{{ $module }}" @selected(($filters['module'] ?? '') === $module)>{{ $module }}</option>
                            @endforeach
                        </select>
                    </x-ui.select-field>
                </div>

                <div class="xl:col-span-2">
                    <x-ui.select-field label="Result">
                        <select name="result" class="select-modern" onchange="this.form.submit()">
                            <option value="">All results</option>
                            @foreach ($results as $result)
                                <option value="{{ $result }}" @selected(($filters['result'] ?? '') === $result)>{{ $result }}</option>
                            @endforeach
                        </select>
                    </x-ui.select-field>
                </div>

                <div class="xl:col-span-2">
                    <x-ui.select-field label="Date range">
                        <select name="date_range" class="select-modern" x-model="dateRange" @change="onDateRangeChange(); $el.closest('form').submit()">
                            @foreach ($dateRanges as $key => $label)
                                <option value="{{ $key }}" @selected(($filters['date_range'] ?? 'today') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </x-ui.select-field>
                </div>

                <div class="xl:col-span-4" x-show="dateRange === 'custom'" x-transition x-cloak>
                    <div class="flex items-end gap-2">
                        <x-ui.form-field label="From">
                            <input type="date" name="date_from" class="input-modern" value="{{ $filters['date_from'] ?? '' }}" max="{{ now()->format('Y-m-d') }}" />
                        </x-ui.form-field>
                        <x-ui.form-field label="To">
                            <input type="date" name="date_to" class="input-modern" value="{{ $filters['date_to'] ?? '' }}" max="{{ now()->format('Y-m-d') }}" />
                        </x-ui.form-field>
                    </div>
                </div>

                <div class="xl:col-span-2">
                    <x-ui.select-field label="User">
                        <select name="user_id" class="select-modern" onchange="this.form.submit()">
                            <option value="">All users</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @selected((string) ($filters['user_id'] ?? '') === (string) $user->id)>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </x-ui.select-field>
                </div>

                <div class="xl:col-span-2 flex items-end gap-2">
                    <a href="{{ route('activity-logs.index') }}" class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                        <x-ui.icon name="x" class="h-4 w-4" />
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </x-ui.card>

    @if ($hasActiveFilters)
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-medium text-slate-400 dark:text-slate-500">Active filters:</span>

            @php
                $removeFilter = static function (string $key) use ($filters, $sort, $perPage): string {
                    $params = array_merge(
                        array_filter($filters, static fn ($value) => ! is_null($value) && $value !== ''),
                        ['sort' => $sort, 'per_page' => $perPage]
                    );
                    unset($params[$key]);

                    return route('activity-logs.index', $params);
                };
            @endphp

            @if (! empty($filters['search']))
                <a href="{{ $removeFilter('search') }}" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 transition hover:border-rose-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-rose-800">
                    Search: {{ \Illuminate\Support\Str::limit($filters['search'], 20) }}
                    <span class="text-rose-500">×</span>
                </a>
            @endif

            @if (! empty($filters['module']))
                <a href="{{ $removeFilter('module') }}" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 transition hover:border-rose-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-rose-800">
                    Module: {{ $filters['module'] }}
                    <span class="text-rose-500">×</span>
                </a>
            @endif

            @if (! empty($filters['result']))
                <a href="{{ $removeFilter('result') }}" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 transition hover:border-rose-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-rose-800">
                    Result: {{ $filters['result'] }}
                    <span class="text-rose-500">×</span>
                </a>
            @endif

            @if (! empty($filters['user_id']))
                @php $userName = $users->firstWhere('id', $filters['user_id'])?->name ?? 'Unknown'; @endphp
                <a href="{{ $removeFilter('user_id') }}" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 transition hover:border-rose-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-rose-800">
                    User: {{ $userName }}
                    <span class="text-rose-500">×</span>
                </a>
            @endif

            @if (! empty($filters['date_range']) && $filters['date_range'] !== 'today')
                @php $rangeLabel = $dateRanges[$filters['date_range']] ?? $filters['date_range']; @endphp
                <a href="{{ $removeFilter('date_range') }}" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600 transition hover:border-rose-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-rose-800">
                    {{ $rangeLabel }}
                    @if (($filters['date_range'] ?? null) === 'custom' && ! empty($filters['date_from']))
                        ({{ $filters['date_from'] }} – {{ $filters['date_to'] ?? 'now' }})
                    @endif
                    <span class="text-rose-500">×</span>
                </a>
            @endif

            <a href="{{ route('activity-logs.index') }}" class="text-xs font-medium text-rose-500 underline-offset-2 hover:text-rose-600 hover:underline">Clear all</a>
        </div>
    @endif

    @if ($logs->count() > 0)
        <div x-show="selectedIds.length > 0" x-transition class="flex items-center gap-3 rounded-2xl border border-indigo-200 bg-indigo-50 px-5 py-3 dark:border-indigo-900 dark:bg-indigo-950/40">
            <span class="text-sm font-semibold text-indigo-700 dark:text-indigo-300"><span x-text="selectedIds.length"></span> entries selected</span>
            <div class="ml-auto flex items-center gap-2">
                <button type="button" @click="confirmBulkDelete()" class="inline-flex items-center gap-2 rounded-2xl border border-rose-200 bg-white px-4 py-2 text-sm font-medium text-rose-700 transition hover:bg-rose-50 dark:border-rose-900 dark:bg-slate-900 dark:text-rose-300 dark:hover:bg-rose-950/30">
                    <x-ui.icon name="trash" class="h-4 w-4" />
                    Delete selected
                </button>
                <button type="button" @click="selectedIds = []; selectAll = false" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                    <x-ui.icon name="x" class="h-4 w-4" />
                    Deselect
                </button>
            </div>
        </div>
    @endif

    <x-ui.table title="Activity log" subtitle="Showing {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }} of {{ number_format($logs->total()) }} entries">
        <thead class="table-head">
            <tr>
                <th class="w-12 px-4 py-4">
                    <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="checkbox-modern" />
                </th>
                <th class="px-4 py-4 text-left">
                    <a href="{{ route('activity-logs.index', array_merge($filters, ['sort' => $sort === 'newest' ? 'oldest' : 'newest', 'per_page' => $perPage])) }}" class="flex items-center gap-1 select-none hover:text-indigo-500">
                        Timestamp <span class="text-xs opacity-50">{{ $sort === 'newest' ? '↓' : '↑' }}</span>
                    </a>
                </th>
                <th class="px-4 py-4 text-left">User</th>
                <th class="px-4 py-4 text-left">Module</th>
                <th class="px-4 py-4 text-left">Action</th>
                <th class="px-4 py-4 text-left">Result</th>
                <th class="hidden px-4 py-4 text-left text-xs xl:table-cell">IP Address</th>
                <th class="px-4 py-4 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse ($logs as $log)
                @php
                    $resultCfg = config('activity.results.' . $log->result, config('activity.results.Success'));
                    $modIcon = config('activity.modules.' . $log->module, 'activity');
                    $name = $log->user_name ?? 'System';
                    $initials = collect(explode(' ', $name))->map(static fn ($word) => strtoupper(substr($word, 0, 1)))->take(2)->implode('');
                @endphp
                <tr class="group transition hover:bg-slate-50/70 dark:hover:bg-slate-800/40" :class="{ 'bg-indigo-50/40 dark:bg-indigo-950/20': selectedIds.includes('{{ $log->id }}') }">
                    <td class="px-4 py-3"><input type="checkbox" value="{{ $log->id }}" x-model="selectedIds" class="checkbox-modern" /></td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="flex flex-col">
                            <span class="font-mono text-sm font-semibold text-slate-900 dark:text-white">{{ $log->formatted_time }}</span>
                            <span class="mt-0.5 font-mono text-[10px] text-slate-400 dark:text-slate-500">{{ $log->created_at->format('d M Y') }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-[10px] font-bold text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-400">{{ $initials }}</div>
                            <span class="font-medium text-sm text-slate-700 dark:text-slate-200">{{ $name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <x-ui.icon name="{{ $modIcon }}" class="h-3.5 w-3.5 shrink-0 text-slate-400 dark:text-slate-500" />
                            <span class="text-sm text-slate-600 dark:text-slate-300">{{ $log->module }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="font-medium text-slate-900 dark:text-white">{{ $log->action }}</span>
                        @if ($log->description)
                            <p class="mt-0.5 max-w-xs truncate text-[11px] leading-relaxed text-slate-400 dark:text-slate-500">{{ $log->description }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium font-mono {{ $resultCfg['bg'] }} {{ $resultCfg['text'] }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $resultCfg['dot'] }}"></span>
                            {{ $log->result }}
                        </span>
                    </td>
                    <td class="hidden px-4 py-3 xl:table-cell"><span class="font-mono text-xs text-slate-400 dark:text-slate-500">{{ $log->ip_address ?? '—' }}</span></td>
                    <td class="px-4 py-3">
                        <div class="relative inline-flex" x-data="{ open: false }">
                            <button type="button" @click="open = !open" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                                Actions <x-ui.icon name="chevron-down" class="h-3 w-3" />
                            </button>
                            <div x-show="open" x-transition @click.outside="open = false" class="absolute right-0 top-9 z-20 w-44 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-xl dark:border-slate-800 dark:bg-slate-950">
                                        @if ($log->metadata)
                                        <button type="button" @click="viewMeta({!! json_encode($log->metadata) !!}, {!! json_encode($log->action) !!}); open = false" class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-sm text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">
                                        <x-ui.icon name="eye" class="h-4 w-4" /> View details
                                    </button>
                                @endif
                                <form method="POST" action="{{ route('activity-logs.destroy', $log) }}" onsubmit="return confirm('Delete this log entry?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-sm text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/30">
                                        <x-ui.icon name="trash" class="h-4 w-4" /> Delete entry
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <x-ui.icon name="inbox" class="h-10 w-10 text-slate-300 dark:text-slate-700" />
                            <p class="text-sm font-semibold text-slate-700 dark:text-white">No activity found</p>
                            <p class="text-xs text-slate-400">Try adjusting your filters or date range.</p>
                            <a href="{{ route('activity-logs.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">Clear filters</a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8" class="border-t border-slate-200 dark:border-slate-800">
                    <div class="flex items-center justify-between gap-4 px-4 py-3">
                        <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                            Show
                            <select class="select-modern-sm" onchange="updatePerPage(this.value)">
                                @foreach ($perPageOptions as $option)
                                    <option value="{{ $option }}" @selected($perPage == $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                            per page
                        </div>
                        <span class="text-sm text-slate-400 dark:text-slate-500">{{ number_format($logs->firstItem() ?? 0) }} – {{ number_format($logs->lastItem() ?? 0) }} of {{ number_format($logs->total()) }}</span>
                        {{ $logs->links() }}
                    </div>
                </td>
            </tr>
        </tfoot>
    </x-ui.table>

    <x-ui.card>
        <div class="mb-5">
            <h2 class="text-base font-semibold text-slate-900 dark:text-white">Activity timeline</h2>
        </div>
        <div class="relative">
            <div class="absolute bottom-0 left-4 top-0 w-px -translate-x-1/2 bg-slate-200 dark:bg-slate-800"></div>
            <div class="space-y-0">
                @forelse ($timeline as $event)
                    @php
                        $resultCfg = config('activity.results.' . $event->result, config('activity.results.Success'));
                        $modIcon = config('activity.modules.' . $event->module, 'activity');
                    @endphp
                    <div class="relative flex items-start gap-4 pb-6 pl-10 last:pb-0">
                        <div class="absolute left-0 flex h-8 w-8 shrink-0 items-center justify-center rounded-full border-2 border-white {{ $resultCfg['bg'] }} dark:border-slate-950">
                            <x-ui.icon name="{{ $modIcon }}" class="h-3.5 w-3.5 {{ $resultCfg['text'] }}" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $event->action }}</p>
                                    @if ($event->description)
                                        <p class="mt-0.5 text-xs leading-relaxed text-slate-400 dark:text-slate-500">{{ $event->description }}</p>
                                    @endif
                                </div>
                                <span class="shrink-0 whitespace-nowrap text-[10px] font-mono text-slate-400 dark:text-slate-500">{{ $event->formatted_date }}</span>
                            </div>
                            <div class="mt-2 flex items-center gap-2">
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-mono text-slate-500 dark:bg-slate-800 dark:text-slate-400">{{ $event->module }}</span>
                                @if ($event->user_name)
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500">by {{ $event->user_name }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-sm text-slate-400 dark:text-slate-500">No activity events found.</div>
                @endforelse
            </div>
        </div>
    </x-ui.card>

    <div x-show="metaModal.open" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" @click.self="metaModal.open = false">
        <div class="w-[480px] max-w-[92vw] rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900">
            <div class="mb-4 flex items-start justify-between">
                <h3 class="font-semibold text-slate-900 dark:text-white">Log detail</h3>
                <button @click="metaModal.open = false" class="text-slate-400 transition hover:text-slate-600 dark:hover:text-slate-200"><x-ui.icon name="x" class="h-5 w-5" /></button>
            </div>
            <p class="mb-3 text-sm font-medium text-slate-600 dark:text-slate-300" x-text="metaModal.action"></p>
            <pre class="max-h-64 overflow-auto rounded-xl bg-slate-50 p-4 text-xs font-mono text-slate-600 dark:bg-slate-800 dark:text-slate-300" x-text="JSON.stringify(metaModal.data, null, 2)"></pre>
            <div class="mt-4 flex justify-end">
                <button @click="metaModal.open = false" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">Close</button>
            </div>
        </div>
    </div>

    <div x-show="deleteModal.open" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="w-[420px] rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-rose-100 dark:bg-rose-950/40"><x-ui.icon name="trash" class="h-5 w-5 text-rose-600 dark:text-rose-400" /></div>
                <div>
                    <h3 class="font-semibold text-slate-900 dark:text-white">Delete log entries?</h3>
                    <p class="mt-0.5 text-sm text-slate-400"><span x-text="selectedIds.length"></span> entries will be permanently deleted.</p>
                </div>
            </div>
            <div class="flex gap-3">
                <button @click="deleteModal.open = false" class="inline-flex flex-1 items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">Cancel</button>
                <button @click="executeBulkDelete()" class="inline-flex flex-1 items-center justify-center rounded-2xl border border-rose-200 bg-white px-4 py-2 text-sm font-medium text-rose-700 transition hover:bg-rose-50 dark:border-rose-900 dark:bg-slate-900 dark:text-rose-300 dark:hover:bg-rose-950/30">Delete</button>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-2xl bg-green-600 px-5 py-3 text-white shadow-xl">
            <x-ui.icon name="check-circle" class="h-5 w-5" />
            {{ session('success') }}
        </div>
    @endif
</section>
@endsection

@push('scripts')
    <script>
function activityLogs() {
    return {
        pageIds: @json($logs->pluck('id')->map(static fn ($id) => (string) $id)->values()->all()),
        selectedIds: [],
        selectAll: false,
        dateRange: @json($filters['date_range'] ?? 'today'),
        metaModal: { open: false, data: null, action: '' },
        deleteModal: { open: false },

        init() {
            this.$watch('selectedIds', (value) => {
                this.selectAll = this.pageIds.length > 0 && value.length === this.pageIds.length;
            });
        },

        onDateRangeChange() {},

        toggleAll() {
            this.selectedIds = this.selectAll ? [...this.pageIds] : [];
        },

        viewMeta(data, action) {
            this.metaModal.data = data;
            this.metaModal.action = action;
            this.metaModal.open = true;
        },

        confirmBulkDelete() {
            this.deleteModal.open = true;
        },

        async executeBulkDelete() {
            if (! this.selectedIds.length) {
                this.deleteModal.open = false;
                return;
            }

            const response = await fetch('{{ route('activity-logs.bulk-destroy') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ ids: this.selectedIds }),
            });

            if (response.ok) {
                window.location.reload();
            }
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