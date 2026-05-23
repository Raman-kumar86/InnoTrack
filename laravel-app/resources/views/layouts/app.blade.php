<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Startup India Progress Dashboard' }}</title>
    <meta name="description" content="Government-grade startup ecosystem analytics dashboard.">
    <script>
        (function () {
            const storageKey = 'startup-india-theme';
            const root = document.documentElement;

            let theme = 'light';

            try {
                const savedTheme = window.localStorage.getItem(storageKey);
                if (savedTheme === 'dark' || savedTheme === 'light') {
                    theme = savedTheme;
                } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    theme = 'dark';
                }
            } catch (error) {
                if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    theme = 'dark';
                }
            }

            root.classList.toggle('dark', theme === 'dark');
            root.setAttribute('data-theme', theme);
        })();
    </script>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @stack('styles')
</head>
<body class="min-h-full bg-[radial-gradient(circle_at_top_left,rgba(79,70,229,0.08),transparent_28%),radial-gradient(circle_at_top_right,rgba(14,165,233,0.08),transparent_24%)] text-slate-900 dark:bg-slate-950 dark:text-slate-100">
    @php($flashErrors = isset($errors) ? $errors : new \Illuminate\Support\ViewErrorBag())
    @if (session('success') || session('error') || session('status') || $flashErrors->any())
        <div class="fixed right-4 top-4 z-50 flex w-[min(24rem,calc(100vw-2rem))] flex-col gap-3">
            @if (session('success'))
                <div data-flash-message class="relative rounded-2xl border border-emerald-200 bg-white px-4 py-3 pr-12 text-sm text-slate-900 shadow-2xl shadow-emerald-950/10 backdrop-blur transition dark:border-emerald-400/20 dark:bg-emerald-500/15 dark:text-emerald-50 dark:shadow-emerald-950/20">
                    {{ session('success') }}
                    <button type="button" data-flash-close class="absolute right-3 top-3 inline-flex h-7 w-7 items-center justify-center rounded-full text-emerald-700 transition hover:bg-slate-100 hover:text-emerald-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40 dark:text-emerald-100 dark:hover:bg-white/10 dark:hover:text-white" aria-label="Dismiss notification">
                        <x-ui.icon name="x" class="h-4 w-4" />
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div data-flash-message class="relative rounded-2xl border border-rose-200 bg-white px-4 py-3 pr-12 text-sm text-slate-900 shadow-2xl shadow-rose-950/10 backdrop-blur transition dark:border-rose-400/20 dark:bg-rose-500/15 dark:text-rose-50 dark:shadow-rose-950/20">
                    {{ session('error') }}
                    <button type="button" data-flash-close class="absolute right-3 top-3 inline-flex h-7 w-7 items-center justify-center rounded-full text-rose-700 transition hover:bg-slate-100 hover:text-rose-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-500/40 dark:text-rose-100 dark:hover:bg-white/10 dark:hover:text-white" aria-label="Dismiss notification">
                        <x-ui.icon name="x" class="h-4 w-4" />
                    </button>
                </div>
            @endif

            @if (session('status'))
                <div data-flash-message class="relative rounded-2xl border border-sky-200 bg-white px-4 py-3 pr-12 text-sm text-slate-900 shadow-2xl shadow-sky-950/10 backdrop-blur transition dark:border-sky-400/20 dark:bg-sky-500/15 dark:text-sky-50 dark:shadow-sky-950/20">
                    {{ session('status') }}
                    <button type="button" data-flash-close class="absolute right-3 top-3 inline-flex h-7 w-7 items-center justify-center rounded-full text-sky-700 transition hover:bg-slate-100 hover:text-sky-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500/40 dark:text-sky-100 dark:hover:bg-white/10 dark:hover:text-white" aria-label="Dismiss notification">
                        <x-ui.icon name="x" class="h-4 w-4" />
                    </button>
                </div>
            @endif

            @if ($flashErrors->any())
                <div data-flash-message class="relative rounded-2xl border border-rose-200 bg-white px-4 py-3 pr-12 text-sm text-slate-900 shadow-2xl shadow-rose-950/10 backdrop-blur transition dark:border-rose-400/20 dark:bg-rose-500/15 dark:text-rose-50 dark:shadow-rose-950/20">
                    {{ $flashErrors->first() }}
                    <button type="button" data-flash-close class="absolute right-3 top-3 inline-flex h-7 w-7 items-center justify-center rounded-full text-rose-700 transition hover:bg-slate-100 hover:text-rose-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-500/40 dark:text-rose-100 dark:hover:bg-white/10 dark:hover:text-white" aria-label="Dismiss notification">
                        <x-ui.icon name="x" class="h-4 w-4" />
                    </button>
                </div>
            @endif
        </div>
    @endif

    <div data-sidebar-overlay class="fixed inset-0 z-40 hidden bg-slate-950/50 backdrop-blur-sm lg:hidden"></div>

    <div class="min-h-screen lg:flex">
        @include('partials.sidebar')

        <div class="flex min-h-screen flex-1 flex-col lg:pl-72">
            @include('partials.navbar')

            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                <div class="mx-auto flex max-w-7xl flex-col gap-6">
                    @isset($breadcrumbs)
                        @include('partials.breadcrumbs', ['items' => $breadcrumbs])
                    @endisset

                    @yield('content')
                </div>
            </main>

            @include('partials.footer')
        </div>
    </div>

    @stack('modals')
    @stack('scripts')
</body>
</html>
