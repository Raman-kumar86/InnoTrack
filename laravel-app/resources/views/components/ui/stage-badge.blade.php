@props([
    'stage' => null,
])

@php
    $label = $stage ?? trim((string) $slot);
    $normalized = str($label)->lower()->trim()->toString();

    $variants = [
        'pre-seed' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
        'seed' => 'bg-sky-500/10 text-sky-700 dark:text-sky-400',
        'angel' => 'bg-indigo-500/10 text-indigo-700 dark:text-indigo-400',
        'series a' => 'bg-violet-500/10 text-violet-700 dark:text-violet-400',
        'series b' => 'bg-purple-500/10 text-purple-700 dark:text-purple-400',
        'series c' => 'bg-fuchsia-500/10 text-fuchsia-700 dark:text-fuchsia-400',
        'bootstrapped' => 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400',
        'ipo' => 'bg-amber-500/10 text-amber-700 dark:text-amber-400',
    ];

    $classes = $variants[$normalized] ?? 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold '.$classes]) }}>
    {{ $label }}
</span>