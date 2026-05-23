@props([
    'title' => null,
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between']) }}>
    <div>
        @if ($title)
            <h2 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">{{ $title }}</h2>
        @endif
        @if ($subtitle)
            <p class="mt-2 max-w-3xl text-sm text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
        @endif
    </div>

    <div class="flex flex-wrap items-center gap-2">
        {{ $slot }}
    </div>
</div>
