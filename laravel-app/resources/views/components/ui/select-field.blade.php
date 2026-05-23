@props(['name' => null, 'label' => null])

<div class="space-y-2">
    @if ($label)
        <label class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $label }}</label>
    @endif
    <select name="{{ $name }}" {{ $attributes->merge(['class' => 'input-modern']) }}>
        {{ $slot }}
    </select>
</div>
