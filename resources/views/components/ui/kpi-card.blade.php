@props([
    'title' => '',
    'value' => '',
    'trend' => '+0%',
    'direction' => 'up',
    'description' => null,
    'sparkLabels' => [],
    'sparkValues' => [],
    'icon' => 'chart',
])

@php
    $trendClass = $direction === 'down'
        ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400'
        : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400';
@endphp

<div {{ $attributes->merge(['class' => 'surface-card overflow-hidden p-6']) }}>
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $title }}</p>
            <div class="mt-2 flex items-end gap-3">
                <span class="text-3xl font-semibold tracking-tight text-slate-900 dark:text-white">{{ $value }}</span>
                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold {{ $trendClass }}">
                    <x-ui.icon name="{{ $direction === 'down' ? 'arrow-down' : 'arrow-up' }}" class="h-3.5 w-3.5" />
                    {{ $trend }}
                </span>
            </div>
            @if ($description)
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $description }}</p>
            @endif
        </div>

        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-600/10 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
            <x-ui.icon name="{{ $icon }}" class="h-6 w-6" />
        </div>
    </div>

    <div class="mt-6 h-16">
        <canvas
            data-chart="sparkline"
            data-labels='@json($sparkLabels)'
            data-values='@json($sparkValues)'
        ></canvas>
    </div>
</div>
