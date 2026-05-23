@props([
    'href' => null,
    'variant' => 'primary',
    'type' => 'button',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-2xl px-4 py-3 text-sm font-semibold transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-indigo-500/20';

    $variants = [
        'primary' => 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20 hover:bg-indigo-500',
        'secondary' => 'bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700',
        'ghost' => 'bg-transparent text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800',
        'danger' => 'bg-rose-600 text-white shadow-lg shadow-rose-600/20 hover:bg-rose-500',
    ];

    $classes = trim($base.' '.($variants[$variant] ?? $variants['primary']));
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
