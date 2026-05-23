<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Startup India Progress Dashboard' }}</title>
    <meta name="description" content="Secure government-grade startup ecosystem portal.">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @stack('styles')
</head>
<body class="min-h-full bg-[radial-gradient(circle_at_top,rgba(15,23,42,0.75),rgba(15,23,42,0.98))] text-slate-100">
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

    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-12 sm:px-6 lg:px-8">
        <div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(79,70,229,0.24),transparent_38%),linear-gradient(315deg,rgba(14,165,233,0.18),transparent_34%)]"></div>
        <div class="absolute left-0 top-0 h-72 w-72 rounded-full bg-indigo-500/20 blur-3xl"></div>
        <div class="absolute bottom-0 right-0 h-72 w-72 rounded-full bg-cyan-500/15 blur-3xl"></div>

        <div class="relative z-10 w-full max-w-md">
            <div class="mb-8 text-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-white/90 backdrop-blur transition hover:border-white/20 hover:bg-white/10">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-linear-to-br from-indigo-500 to-cyan-500 text-white shadow-lg shadow-indigo-600/30">
                        <x-ui.icon name="shield" class="h-5 w-5" />
                    </span>
                    Startup India Progress Dashboard
                </a>
            </div>

            <div class="glass-card border-white/10 bg-slate-900/80 p-6 sm:p-8">
                @yield('content')
            </div>
        </div>
    </div>

    @stack('modals')
    @stack('scripts')
</body>
</html>
