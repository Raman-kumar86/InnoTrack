@props([
    'variant' => 'neutral',
])

@php
    $variants = [
        'neutral' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300',
        'success' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
        'warning' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
        'danger' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400',
        'info' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold '.$variants[$variant]]) }}>
    {{ $slot }}
</span>
