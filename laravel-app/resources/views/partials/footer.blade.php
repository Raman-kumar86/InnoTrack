@php
    $isLanding = request()->is('/') || request()->routeIs('home');
@endphp

@if ($isLanding)
    <!-- ====================== LANDING PAGE FOOTER ====================== -->
    <footer class="bg-white border-t border-slate-200 py-8">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-slate-500 text-sm">
                &copy; {{ date('Y') }} Startup India Progress Dashboard. 
                All Rights Reserved.
            </p>
            <p class="text-xs text-slate-400 mt-2">
                A Government of India Initiative
            </p>
        </div>
    </footer>

@else
<footer class="border-t border-slate-200/80 bg-white/60 px-4 py-5 text-sm text-slate-500 backdrop-blur dark:border-slate-800 dark:bg-slate-950/60 dark:text-slate-400 sm:px-6 lg:px-8">
    <div class="mx-auto flex max-w-7xl flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p>Startup India Progress Dashboard</p>
        <p>Designed for high-trust public sector analytics and modern startup operations.</p>
    </div>
</footer>
@endif