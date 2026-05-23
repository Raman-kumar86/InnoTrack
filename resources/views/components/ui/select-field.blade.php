@props([
    'label' => null,
    'help' => null,
    'error' => null,
])

<div {{ $attributes->merge(['class' => 'space-y-2']) }}>
    @if ($label)
        <label class="label-modern">{{ $label }}</label>
    @endif

    <div class="relative">
        {{ $slot }}
    </div>

    @if ($help)
        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $help }}</p>
    @endif

    @if ($error)
        <p class="text-xs font-medium text-rose-500">{{ $error }}</p>
    @endif
</div>
