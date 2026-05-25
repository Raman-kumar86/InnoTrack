@php
$navigation = [
['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'dashboard'],
['label' => 'Startups', 'route' => 'startups.index', 'icon' => 'rocket'],
['label' => 'State Analytics', 'route' => 'state-analytics.index', 'icon' => 'map'],
['label' => 'Reports', 'route' => 'reports.index', 'icon' => 'reports'],
['label' => 'User Management', 'route' => 'users.index', 'icon' => 'users'],
['label' => 'Activity Logs', 'route' => 'activity-logs.index', 'icon' => 'activity'],
['label' => 'Settings', 'route' => 'settings.index', 'icon' => 'settings'],
];
@endphp

<aside
    data-sidebar
    x-cloak
    class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full border-r border-slate-200/80 bg-white/95 backdrop-blur-xl transition-all duration-300 ease-in-out dark:border-slate-800 dark:bg-slate-950/95 lg:translate-x-0"
    :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    @click.outside="closeMobileSidebar()"
>
    <div class="flex h-full flex-col">
        <div class="flex items-center justify-between gap-3 border-b border-slate-200/80 px-4 py-5 dark:border-slate-800 lg:px-5">
            <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-indigo-600 via-blue-600 to-cyan-500 text-white shadow-lg shadow-indigo-600/30">
                    <x-ui.icon name="shield" class="h-6 w-6" />
                </span>
                <span class="min-w-0 max-w-56 overflow-hidden transition-all duration-300 ease-in-out">
                    <span class="block text-xs font-semibold uppercase tracking-[0.25em] text-indigo-600 dark:text-indigo-400">Startup India</span>
                    <span class="block truncate text-lg font-semibold text-slate-900 dark:text-white">Progress Dashboard</span>
                </span>
            </a>
        </div>

        <div class="flex-1 overflow-y-auto px-3 py-5 lg:px-3">

            <nav class="space-y-2">
                @foreach ($navigation as $item)
                @php($isActive = request()->routeIs($item['route']))
                <a
                    href="{{ route($item['route']) }}"
                    class="group relative flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-medium transition-all duration-300 ease-in-out {{ $isActive ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-white' }}"
                    title="{{ $item['label'] }}"
                    aria-label="{{ $item['label'] }}"
                >
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl transition-all duration-300 ease-in-out {{ $isActive ? 'bg-white/15' : 'bg-slate-100 dark:bg-slate-800' }}">
                        <x-ui.icon name="{{ $item['icon'] }}" class="h-5 w-5" />
                    </span>

                    <span class="min-w-0 flex-1 max-w-40 overflow-hidden whitespace-nowrap transition-all duration-300 ease-in-out">
                        {{ $item['label'] }}
                    </span>

                    @if ($isActive)
                        <span class="h-2 w-2 shrink-0 rounded-full bg-emerald-400 transition-opacity duration-300 ease-in-out"></span>
                    @endif
                </a>
                @endforeach
            </nav>
        </div>
    </div>
</aside>