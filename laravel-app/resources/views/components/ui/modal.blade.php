@props(['id' => null, 'title' => null])

<div id="{{ $id }}" data-modal class="modal hidden fixed inset-0 z-50 items-center justify-center p-4">
    <div class="modal-backdrop absolute inset-0 bg-black/50"></div>
    <div class="relative z-10 w-full max-w-2xl">
        <div class="surface-card overflow-hidden p-6">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">{{ $title }}</h3>
                <button type="button" data-modal-close class="text-slate-400 hover:text-slate-700">&times;</button>
            </div>

            <div class="mt-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
