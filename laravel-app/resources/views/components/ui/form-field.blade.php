@props([
    'label' => null,
    'help' => null,
    'name' => null,
    'type' => 'text',
    'value' => null,
])

@php
    $slotContent = trim((string) $slot);
    $hasSlotContent = $slotContent !== '';
@endphp

<div {{ $attributes->merge(['class' => 'space-y-2']) }}>
    @if ($label)
        <label for="{{ $name }}" class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $label }}</label>
    @endif

    @if ($hasSlotContent)
        {{ $slot }}
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ $value }}" {{ $attributes->merge(['class' => 'input-modern']) }} />
    @endif

    @if ($help)
        <p class="text-xs leading-5 text-slate-500 dark:text-slate-400">{{ $help }}</p>
    @endif
</div>
