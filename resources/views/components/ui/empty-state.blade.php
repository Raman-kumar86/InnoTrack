@props([
    'title' => 'Nothing to show yet',
    'description' => null,
    'action' => null,
    'icon' => 'circle',
])

<div {{ $attributes->merge(['class' => 'surface-card flex flex-col items-center justify-center px-6 py-16 text-center']) }}>
    <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-indigo-600/10 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
        <x-ui.icon name="{{ $icon }}" class="h-8 w-8" />
    </div>
    <h3 class="mt-5 text-lg font-semibold text-slate-900 dark:text-white">{{ $title }}</h3>
    @if ($description)
        <p class="mt-2 max-w-md text-sm text-slate-500 dark:text-slate-400">{{ $description }}</p>
    @endif
    @if ($action)
        <div class="mt-6">{{ $action }}</div>
    @endif
</div>
