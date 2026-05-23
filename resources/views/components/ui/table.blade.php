@props([
    'title' => null,
    'subtitle' => null,
    'actions' => null,
])

<div {{ $attributes->merge(['class' => 'surface-card overflow-hidden']) }}>
    @if ($title || $subtitle || $actions)
        <div class="flex flex-col gap-4 border-b border-slate-200 px-6 py-5 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
            <div>
                @if ($title)
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
                @endif
            </div>

            @if ($actions)
                <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
            @endif
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="table-modern">
            {{ $slot }}
        </table>
    </div>
</div>
