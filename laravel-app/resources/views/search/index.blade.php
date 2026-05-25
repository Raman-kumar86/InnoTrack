@extends('layouts.app')

@php
    $title = $title ?? 'Search';
    $pageTitle = $pageTitle ?? 'Search';
    $breadcrumbs = $breadcrumbs ?? [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Search', 'url' => route('search.index')],
    ];
@endphp

@section('content')
<section class="space-y-6">
    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-2">
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-cyan-600 dark:text-cyan-300">Global search</p>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Search the portal</h2>
                <p class="max-w-2xl text-sm leading-6 text-slate-500 dark:text-slate-400">Find startups, states, users, activity logs, and the main navigation pages from one place.</p>
            </div>

            <form method="GET" action="{{ route('search.index') }}" class="w-full max-w-xl">
                <label for="page-search" class="sr-only">Search</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                        <x-ui.icon name="search" class="h-5 w-5" />
                    </span>
                    <input
                        id="page-search"
                        name="query"
                        type="search"
                        value="{{ $query }}"
                        placeholder="Search startups, states, reports..."
                        class="input-modern pl-12 pr-28"
                        autofocus
                    />
                    <button type="submit" class="absolute inset-y-1 right-1 rounded-2xl bg-slate-900 px-4 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">Search</button>
                </div>
            </form>
        </div>

        <div class="mt-5 flex flex-wrap items-center gap-3 text-sm text-slate-500 dark:text-slate-400">
            <span class="rounded-full bg-slate-100 px-3 py-1 font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-200">{{ number_format($totalResults) }} results</span>
            @if ($query !== '')
                <span>for “{{ $query }}”</span>
            @else
                <span>Try a startup name, state, user, or report term.</span>
            @endif
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($destinations as $destination)
            <a href="{{ $destination['route'] }}" class="group rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-cyan-300 hover:shadow-lg dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-base font-semibold text-slate-900 transition group-hover:text-cyan-700 dark:text-white dark:group-hover:text-cyan-300">{{ $destination['title'] }}</p>
                        <p class="mt-1 text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $destination['subtitle'] }}</p>
                    </div>
                    <span class="rounded-2xl bg-cyan-500/10 px-3 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-cyan-700 dark:text-cyan-300">Open</span>
                </div>
            </a>
        @endforeach
    </div>

    @if ($query !== '')
        <div class="space-y-5">
            <div class="grid gap-5 xl:grid-cols-2">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Startups</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Matching startup records</p>
                        </div>
                        <span class="rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300">{{ number_format($startupResults->count()) }}</span>
                    </div>

                    <div class="mt-4 space-y-3">
                        @forelse ($startupResults as $startup)
                            <a href="{{ route('startups.show', $startup) }}" class="block rounded-2xl border border-slate-200 p-4 transition hover:border-cyan-300 hover:bg-cyan-50/40 dark:border-slate-800 dark:hover:border-cyan-700 dark:hover:bg-slate-800/60">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-slate-900 dark:text-white">{{ $startup->startup_name }}</p>
                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $startup->sector?->sector_name ?? 'Sector not assigned' }} · {{ $startup->state?->state_name ?? 'State not assigned' }}</p>
                                    </div>
                                    <span class="shrink-0 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $startup->status }}</span>
                                </div>
                            </a>
                        @empty
                            <p class="rounded-2xl border border-dashed border-slate-300 px-4 py-8 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">No startups matched this search.</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">States</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Matching state names</p>
                        </div>
                        <span class="rounded-full bg-cyan-500/10 px-3 py-1 text-xs font-semibold text-cyan-700 dark:text-cyan-300">{{ number_format($stateResults->count()) }}</span>
                    </div>

                    <div class="mt-4 space-y-3">
                        @forelse ($stateResults as $state)
                            <a href="{{ route('startups.index', ['state_id' => $state->id, 'search' => $query]) }}" class="block rounded-2xl border border-slate-200 p-4 transition hover:border-cyan-300 hover:bg-cyan-50/40 dark:border-slate-800 dark:hover:border-cyan-700 dark:hover:bg-slate-800/60">
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $state->state_name }}</p>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Open startup registry filtered to this state.</p>
                            </a>
                        @empty
                            <p class="rounded-2xl border border-dashed border-slate-300 px-4 py-8 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">No states matched this search.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            @if ($userResults->isNotEmpty() || $activityResults->isNotEmpty())
                <div class="grid gap-5 xl:grid-cols-2">
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Users</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Matching portal users</p>
                            </div>
                            <span class="rounded-full bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-700 dark:text-amber-300">{{ number_format($userResults->count()) }}</span>
                        </div>

                        <div class="mt-4 space-y-3">
                            @forelse ($userResults as $resultUser)
                                <a href="{{ route('users.index', ['search' => $query]) }}" class="block rounded-2xl border border-slate-200 p-4 transition hover:border-cyan-300 hover:bg-cyan-50/40 dark:border-slate-800 dark:hover:border-cyan-700 dark:hover:bg-slate-800/60">
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $resultUser->name }}</p>
                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $resultUser->email }} · {{ $resultUser->role }}</p>
                                </a>
                            @empty
                                <p class="rounded-2xl border border-dashed border-slate-300 px-4 py-8 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">No users matched this search.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Activity logs</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Matching audit entries</p>
                            </div>
                            <span class="rounded-full bg-rose-500/10 px-3 py-1 text-xs font-semibold text-rose-700 dark:text-rose-300">{{ number_format($activityResults->count()) }}</span>
                        </div>

                        <div class="mt-4 space-y-3">
                            @forelse ($activityResults as $activity)
                                <a href="{{ route('activity-logs.index', ['search' => $query]) }}" class="block rounded-2xl border border-slate-200 p-4 transition hover:border-cyan-300 hover:bg-cyan-50/40 dark:border-slate-800 dark:hover:border-cyan-700 dark:hover:bg-slate-800/60">
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $activity->description ?? $activity->action }}</p>
                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $activity->module ?? 'General' }} · {{ $activity->relative_time ?? $activity->formatted_time }}</p>
                                </a>
                            @empty
                                <p class="rounded-2xl border border-dashed border-slate-300 px-4 py-8 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">No activity logs matched this search.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif

            @if ($startupResults->isEmpty() && $stateResults->isEmpty() && $userResults->isEmpty() && $activityResults->isEmpty())
                <div class="rounded-[2rem] border border-dashed border-slate-300 bg-white p-8 text-center shadow-sm dark:border-slate-700 dark:bg-slate-900">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">No results found</p>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Try a broader keyword or search one of the quick destinations above.</p>
                </div>
            @endif
        </div>
    @endif
</section>
@endsection