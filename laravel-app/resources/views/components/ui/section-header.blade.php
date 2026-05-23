@props(['title' => null, 'subtitle' => null])

<div class="section-header">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $title }}</h2>
            @if ($subtitle)
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
            @endif
        </div>
        <div>
            {{ $slot }}
        </div>
    </div>
</div>
