<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Startup India Progress Dashboard' }}</title>
    <meta name="description" content="Government-grade startup ecosystem analytics dashboard.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-full bg-[radial-gradient(circle_at_top_left,_rgba(79,70,229,0.08),_transparent_28%),radial-gradient(circle_at_top_right,_rgba(14,165,233,0.08),_transparent_24%)] text-slate-900 dark:bg-slate-950 dark:text-slate-100">
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
