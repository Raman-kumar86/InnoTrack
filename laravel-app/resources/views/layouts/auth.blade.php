<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Startup India Progress Dashboard' }}</title>
    <meta name="description" content="Secure government-grade startup ecosystem portal.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-full bg-[radial-gradient(circle_at_top,_rgba(15,23,42,0.75),_rgba(15,23,42,0.98))] text-slate-100">
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-12 sm:px-6 lg:px-8">
        <div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(79,70,229,0.24),transparent_38%),linear-gradient(315deg,rgba(14,165,233,0.18),transparent_34%)]"></div>
        <div class="absolute left-0 top-0 h-72 w-72 rounded-full bg-indigo-500/20 blur-3xl"></div>
        <div class="absolute bottom-0 right-0 h-72 w-72 rounded-full bg-cyan-500/15 blur-3xl"></div>

        <div class="relative z-10 w-full max-w-md">
            <div class="mb-8 text-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-white/90 backdrop-blur transition hover:border-white/20 hover:bg-white/10">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-cyan-500 text-white shadow-lg shadow-indigo-600/30">
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
