@php
    $items = $items ?? [];
@endphp

<nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
    @foreach ($items as $index => $item)
        <a href="{{ $item['url'] ?? '#' }}" class="transition hover:text-slate-900 dark:hover:text-white">
            {{ $item['label'] }}
        </a>
        @if (! $loop->last)
            <x-ui.icon name="chevron-right" class="h-4 w-4 text-slate-400" />
        @endif
    @endforeach
</nav>
