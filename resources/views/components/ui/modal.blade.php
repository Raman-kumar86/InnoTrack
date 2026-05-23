@props([
    'id' => null,
    'title' => null,
    'open' => false,
])

<div id="{{ $id }}" data-modal role="dialog" aria-modal="true" class="fixed inset-0 z-50 {{ $open ? 'flex' : 'hidden' }} items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
    <div class="surface-card w-full max-w-2xl p-6 shadow-2xl shadow-slate-950/30">
        <div class="flex items-start justify-between gap-4 border-b border-slate-200 pb-4 dark:border-slate-800">
            <div>
                @if ($title)
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $title }}</h3>
                @endif
            </div>
            <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 text-slate-500 transition hover:text-slate-900 dark:border-slate-800 dark:text-slate-400 dark:hover:text-white" data-modal-close>
                <x-ui.icon name="x" class="h-5 w-5" />
            </button>
        </div>
        <div class="pt-5">
            {{ $slot }}
        </div>
    </div>
</div>
