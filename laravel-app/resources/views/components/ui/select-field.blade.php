@props(['name' => null, 'label' => null, 'error' => null])

<div class="space-y-2">
    @if ($label)
        <label class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $label }}</label>
    @endif
    <select name="{{ $name }}" {{ $attributes->merge(['class' => 'input-modern '.($error ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-500/10' : '')]) }}>
        {{ $slot }}
    </select>

    @if ($error)
        <p class="text-xs font-medium text-rose-500">{{ $error }}</p>
    @endif
</div>
