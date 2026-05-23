@props([
    'title' => null,
    'label' => null,
    'value' => null,
    'kpi' => null,
    'trend' => null,
    'delta' => null,
    'icon' => null,
    'description' => null,
])

@php
    $label = $label ?? $title;
    $kpiValue = $kpi ?? $value;
    $delta = $delta ?? $trend;
@endphp

<div class="kpi-card p-4">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $label }}</p>
            <p class="mt-1 text-xl font-semibold text-slate-900 dark:text-white">{{ $kpiValue }}</p>
            @if ($description)
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $description }}</p>
            @endif
        </div>

        @if ($icon)
            <div class="shrink-0 rounded-xl bg-slate-100 p-3 dark:bg-slate-800">
                <x-ui.icon name="{{ $icon }}" class="h-5 w-5" />
            </div>
        @endif
    </div>

    @if ($delta)
        <div class="mt-3 text-sm font-semibold text-emerald-600">{{ $delta }}</div>
    @endif
</div>
