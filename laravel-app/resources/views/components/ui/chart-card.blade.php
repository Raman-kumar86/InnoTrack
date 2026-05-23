@props([
    'title' => '',
    'subtitle' => null,
    'action' => null,
    'height' => 'h-72',
])

<div {{ $attributes->merge(['class' => 'surface-card overflow-hidden p-6']) }}>
    <div class="flex items-start justify-between gap-4">
        <div>
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $title }}</h3>
            @if ($subtitle)
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
            @endif
        </div>

        @if ($action)
            <div class="shrink-0">{{ $action }}</div>
        @endif
    </div>

    <div class="mt-5 {{ $height }}">
        {{ $slot }}
    </div>
</div>
