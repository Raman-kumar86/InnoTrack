@props([
    'padding' => 'p-6',
])

<div {{ $attributes->merge(['class' => 'surface-card overflow-hidden '.$padding]) }}>
    {{ $slot }}
</div>
