<header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/80 backdrop-blur-xl dark:border-slate-800 dark:bg-slate-950/75">
    <div class="flex h-20 items-center gap-4 px-4 sm:px-6 lg:px-8">
        <button type="button" data-sidebar-open class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 lg:hidden">
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
            <label class="relative block">
                <span class="sr-only">Search</span>
                <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                    <x-ui.icon name="search" class="h-5 w-5" />
                </span>
                <input type="search" placeholder="Search startups, states, reports..." class="input-modern pl-12" />
            </label>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" data-theme-toggle class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200">
                <x-ui.icon name="sun" class="h-5 w-5 text-amber-500 dark:hidden" />
                <x-ui.icon name="moon" class="hidden h-5 w-5 text-slate-300 dark:block" />
            </button>

            <button type="button" class="relative inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200">
                <x-ui.icon name="bell" class="h-5 w-5" />
                <span class="absolute right-2 top-2 h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-slate-950"></span>
            </button>

            <div class="relative">
                <div class="relative js-profile">
                    <button type="button" class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:hover:bg-slate-800" aria-expanded="false">
                        @php
                            $fullName = trim((string) (Auth::user()->name ?? ''));
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
        </div>
    </div>
</header>