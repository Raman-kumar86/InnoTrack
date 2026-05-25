@php
$isLanding = request()->is('/') || request()->routeIs('home');
@endphp

@if ($isLanding)
<!-- LANDING PAGE NAVBAR -->
<nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex h-20 items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-blue-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner">
                    🚀
                </div>
                <div class="font-bold tracking-tighter">
                    <span class="text-2xl text-slate-900">Startup</span>
                    <span class="text-2xl text-indigo-600">India</span>
                </div>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden lg:flex items-center gap-8 text-sm font-medium">
                <a href="#" class="hover:text-indigo-600 transition">Home</a>
                <a href="#" class="hover:text-indigo-600 transition">Features</a>
                <a href="{{ route('search.index') }}" class="hover:text-indigo-600 transition">Analytics</a>
                <a href="#" class="hover:text-indigo-600 transition">Dashboard</a>
                <a href="#" class="hover:text-indigo-600 transition">Reports</a>
            </div>

            <div class="flex items-center gap-4">
                @guest
                <a href="/auth/login" class="hidden sm:block text-sm font-medium text-slate-600 hover:text-slate-900">Login</a>
                <a href="/auth/login"
                    class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-2xl transition shadow">
                    Login to Dashboard
                </a>
                @else
                <a href="/dashboard" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-2xl transition">Go to Dashboard</a>
                @endguest

                <button id="mobile-btn" class="lg:hidden p-3">
                    <x-ui.icon name="menu" class="h-6 w-6" />
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden lg:hidden bg-white border-t">
        <div class="px-6 py-8 space-y-6 text-base font-medium">
            <a href="#" class="block">Home</a>
            <a href="#" class="block">Features</a>
            <a href="{{ route('search.index') }}" class="block">Analytics</a>
            <a href="#" class="block">Dashboard</a>
            <a href="#" class="block">Reports</a>
        </div>
    </div>
</nav>

<script>
    document.getElementById('mobile-btn').addEventListener('click', () => {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
</script>

@else
<header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/80 backdrop-blur-xl dark:border-slate-800 dark:bg-slate-950/75">
    <div class="flex h-20 items-center gap-4 px-4 sm:px-6 lg:px-8">
        <button
            type="button"
            class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 lg:hidden"
            @click="toggleMobileSidebar()"
            aria-label="Toggle sidebar"
            :aria-expanded="mobileSidebarOpen.toString()">
            <x-ui.icon name="menu" class="h-5 w-5" />
        </button>

        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400">
                <span class="hidden sm:inline">Government of India</span>
                <span class="hidden h-1 w-1 rounded-full bg-slate-400 sm:inline"></span>
                <span class="truncate font-medium text-slate-700 dark:text-slate-200">Startup India ecosystem operations</span>
            </div>
            <h1 class="mt-1 truncate text-lg font-semibold text-slate-900 dark:text-white">{{ $pageTitle ?? 'Dashboard' }}</h1>
        </div>

        <div class="hidden max-w-xl flex-1 lg:block">
            <form method="GET" action="{{ route('search.index') }}" class="relative block">
                <label for="navbar-search" class="sr-only">Search</label>
                <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                    <x-ui.icon name="search" class="h-5 w-5" />
                </span>
                <input
                    id="navbar-search"
                    name="query"
                    type="search"
                    value="{{ request('query') }}"
                    placeholder="Search startups, states, reports..."
                    class="input-modern pl-12" />
            </form>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" data-theme-toggle class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200">
                <x-ui.icon name="sun" class="h-5 w-5 text-amber-500 dark:hidden" />
                <x-ui.icon name="moon" class="hidden h-5 w-5 text-slate-300 dark:block" />
            </button>

            <div class="relative" x-data="{ open: false }">
                <button type="button" @click="open = !open" class="relative inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200" :aria-expanded="open.toString()" aria-haspopup="true">
                    <x-ui.icon name="bell" class="h-5 w-5" />
                    @if (($navUnreadNotificationCount ?? 0) > 0)
                    <span class="absolute right-2 top-2 min-h-2.5 min-w-2.5 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-slate-950"></span>
                    @endif
                </button>

                <div x-cloak x-show="open" x-transition.origin.top.right @click.outside="open = false" class="absolute right-0 mt-2 w-[22rem] overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-slate-800">
                        <div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">Notifications</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ number_format($navUnreadNotificationCount ?? 0) }} unread</p>
                        </div>

                        @if (($navUnreadNotificationCount ?? 0) > 0)
                        <form method="POST" action="{{ route('notifications.read-all') }}">
                            @csrf
                            <button type="submit" class="rounded-2xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Mark all read</button>
                        </form>
                        @endif
                    </div>

                    <div class="max-h-96 overflow-y-auto">
                        @forelse (($navNotifications ?? collect()) as $notification)
                        <form method="POST" action="{{ route('notifications.read', $notification) }}" class="border-b border-slate-100 dark:border-slate-800 last:border-b-0">
                            @csrf
                            <button type="submit" class="flex w-full gap-3 px-4 py-3 text-left transition hover:bg-slate-50 dark:hover:bg-slate-800/70">
                                <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl {{ $notification->is_read ? 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' : 'bg-emerald-500/10 text-emerald-500 dark:bg-emerald-500/15 dark:text-emerald-300' }}">
                                    <x-ui.icon name="{{ $notification->notification_type === 'funding_update' ? 'funding' : 'bell' }}" class="h-5 w-5" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <p class="truncate text-sm font-semibold {{ $notification->is_read ? 'text-slate-700 dark:text-slate-200' : 'text-slate-900 dark:text-white' }}">{{ $notification->title }}</p>
                                        <span class="shrink-0 rounded-full px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.2em] {{ $notification->priority === 'high' ? 'bg-yellow-500/10 text-yellow-600 dark:text-yellow-300' : ($notification->priority === 'medium' ? 'bg-cyan-500/10 text-cyan-600 dark:text-cyan-300' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400') }}">{{ $notification->priority }}</span>
                                    </div>
                                    <p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500 dark:text-slate-400">{{ $notification->message }}</p>
                                    <div class="mt-2 flex items-center gap-2 text-[11px] text-slate-400 dark:text-slate-500">
                                        <span class="font-medium text-cyan-600 dark:text-cyan-300">{{ $notification->relative_time }}</span>
                                        @if ($notification->startup?->startup_name)
                                        <span class="truncate">• {{ $notification->startup->startup_name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </button>
                        </form>
                        @empty
                        <div class="px-4 py-10 text-center">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 text-slate-400 dark:border-slate-800 dark:bg-slate-800 dark:text-slate-500">
                                <x-ui.icon name="inbox" class="h-6 w-6" />
                            </div>
                            <p class="mt-3 text-sm font-semibold text-slate-700 dark:text-white">No notifications yet</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">You’ll see startup updates and system alerts here.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            @auth
            <div class="relative">
                <div class="relative js-profile">
                    <button type="button" class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:hover:bg-slate-800" aria-expanded="false">
                        @php
                        $fullName = trim((string) (auth()->user()?->name ?? ''));
                        $first = $fullName === '' ? '' : explode(' ', $fullName)[0];
                        $initial = $first !== '' ? strtoupper(substr($first, 0, 1)) : 'G';
                        @endphp
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-linear-to-br from-indigo-600 to-cyan-500 text-sm font-semibold text-white">{{ $initial }}</span>
                        <x-ui.icon name="chevron-down" class="h-4 w-4 text-slate-400 transition-transform duration-200 js-profile-chevron" />
                    </button>

                    <div class="pointer-events-none opacity-0 transform translate-y-1 transition-all duration-150 absolute right-0 mt-2 w-72 rounded-2xl border border-slate-200 bg-white shadow-lg dark:border-slate-800 dark:bg-slate-900 js-profile-menu">
                        <div class="p-4">
                            <div class="grid gap-1">
                                <a href="{{ route('profile.show') }}" class="flex items-center gap-2 rounded px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800">
                                    <x-ui.icon name="user" class="h-4 w-4 text-slate-400" />
                                    Profile
                                </a>
                                <a href="{{ route('settings.index') }}" class="flex items-center gap-2 rounded px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800">
                                    <x-ui.icon name="settings" class="h-4 w-4 text-slate-400" />
                                    Settings
                                </a>
                                <form method="POST" action="{{ route('auth.logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 rounded px-3 py-2 text-sm text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-slate-800">
                                        <x-ui.icon name="logout" class="h-4 w-4" />
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    (function() {
                        try {
                            const container = document.querySelector('.js-profile');
                            if (!container) return;
                            const btn = container.querySelector('button');
                            const menu = container.querySelector('.js-profile-menu');
                            const chevron = container.querySelector('.js-profile-chevron');
                            let clickedOpen = false;

                            function openMenu() {
                                menu.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-1');
                                menu.classList.add('opacity-100', 'pointer-events-auto', 'translate-y-0');
                                chevron?.classList.add('rotate-180');
                                btn.setAttribute('aria-expanded', 'true');
                            }

                            function closeMenu() {
                                menu.classList.add('opacity-0', 'pointer-events-none', 'translate-y-1');
                                menu.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                                chevron?.classList.remove('rotate-180');
                                btn.setAttribute('aria-expanded', 'false');
                            }

                            // Click/tap behavior
                            btn.addEventListener('click', (e) => {
                                e.preventDefault();
                                clickedOpen = !clickedOpen;
                                if (clickedOpen) openMenu();
                                else closeMenu();
                            });

                            // Close when clicking outside
                            document.addEventListener('click', (e) => {
                                if (!container.contains(e.target)) {
                                    clickedOpen = false;
                                    closeMenu();
                                }
                            });
                        } catch (err) {
                            // silent
                        }
                    })();
                </script>
            </div>
            @else
            <div class="flex items-center gap-2">
                <a href="/auth/login" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200">Login</a>
                <a href="/auth/register" class="inline-flex items-center gap-2 rounded-2xl bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700">Register</a>
            </div>
            @endauth
        </div>
    </div>
</header>
@endif