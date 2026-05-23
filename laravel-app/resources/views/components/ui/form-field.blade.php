@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'value' => null,
])

<div class="space-y-2">
    @if ($label)
        <label for="{{ $name }}" class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $label }}</label>
    @endif
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ $value }}" {{ $attributes->merge(['class' => 'input-modern']) }} />
</div>
