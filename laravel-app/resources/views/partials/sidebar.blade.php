@php
$navigation = [
['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'dashboard'],
['label' => 'Startups', 'route' => 'startups.index', 'icon' => 'rocket'],
['label' => 'Funding Tracker', 'route' => 'funding.create', 'icon' => 'funding'],
['label' => 'State Analytics', 'route' => 'analytics.state', 'icon' => 'map'],
['label' => 'Reports', 'route' => 'reports.index', 'icon' => 'reports'],
['label' => 'User Management', 'route' => 'users.index', 'icon' => 'users'],
['label' => 'Activity Logs', 'route' => 'activity.index', 'icon' => 'activity'],
['label' => 'Settings', 'route' => 'settings.index', 'icon' => 'settings'],
];
@endphp

<aside data-sidebar class="fixed inset-y-0 left-0 z-50 w-72 -translate-x-full border-r border-slate-200/80 bg-white/95 backdrop-blur-xl transition-transform duration-300 ease-out dark:border-slate-800 dark:bg-slate-950/95 lg:translate-x-0">
    <div class="flex h-full flex-col">
        <div class="flex items-center justify-between border-b border-slate-200/80 px-6 py-5 dark:border-slate-800">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-linear-to-br from-indigo-600 via-blue-600 to-cyan-500 text-white shadow-lg shadow-indigo-600/30">
                    <x-ui.icon name="shield" class="h-6 w-6" />
                </span>
                <span>
                    <span class="block text-sm font-semibold uppercase tracking-[0.25em] text-indigo-600 dark:text-indigo-400">Startup India</span>
                    <span class="block text-lg font-semibold text-slate-900 dark:text-white">Progress Dashboard</span>
                </span>
            </a>

            <button type="button" data-sidebar-close class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-600 transition hover:border-slate-300 hover:text-slate-900 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 lg:hidden">
                <x-ui.icon name="x" class="h-5 w-5" />
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-5">

            <nav class="space-y-2">
                @foreach ($navigation as $item)
                <a href="{{ route($item['route']) }}" class="sidebar-link {{ request()->routeIs($item['route']) ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl {{ request()->routeIs($item['route']) ? 'bg-white/15' : 'bg-slate-100 dark:bg-slate-800' }}">
                        <x-ui.icon name="{{ $item['icon'] }}" class="h-5 w-5" />
                    </span>
                    <span class="flex-1">{{ $item['label'] }}</span>
                    @if (request()->routeIs($item['route']))
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    @endif
                </a>
                @endforeach
            </nav>
        </div>

        <div class="border-t border-slate-200/80 p-4 dark:border-slate-800">
            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900/80">
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500 dark:text-slate-400">System status</p>
                <div class="mt-3 flex items-center justify-between text-sm">
                    <span class="text-slate-600 dark:text-slate-300">Data pipeline</span>
                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Online
                    </span>
                </div>
            </div>
        </div>
    </div>
</aside>