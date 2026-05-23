@props([
    'name' => null,
    'label' => null,
    'type' => 'text',
    'value' => null,
    'toggle' => false,
    'error' => null,
    'autocomplete' => null,
])

<div class="space-y-2">
    <div class="relative">
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ $value }}"
            autocomplete="{{ $autocomplete }}"
            placeholder=" "
            class="peer input-modern pt-6 pb-2 {{ $error ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-500/10' : '' }}"
            {{ $attributes }}
        >
        <label for="{{ $name }}" class="pointer-events-none absolute left-4 top-3 text-xs font-medium text-slate-500 transition-all peer-placeholder-shown:top-4 peer-placeholder-shown:text-sm peer-placeholder-shown:text-slate-400 peer-focus:top-3 peer-focus:text-xs peer-focus:text-indigo-600 dark:text-slate-400 dark:peer-focus:text-indigo-400">
            {{ $label }}
        </label>

        @if ($toggle)
            <button
                type="button"
                data-password-toggle="#{{ $name }}"
                class="absolute right-3 top-3 inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300"
            >
                <x-ui.icon name="eye" class="h-4 w-4" />
                Show
            </button>
        @endif
    </div>

    @if ($error)
        <p class="text-xs font-medium text-rose-500">{{ $error }}</p>
    @endif
</div>
