@props(['variant' => 'primary', 'type' => 'button'])

@php
    $variants = [
        'primary' => 'bg-basil text-white hover:bg-basil-dark',
        'secondary' => 'border border-line bg-surface text-ink hover:bg-paper',
        'danger' => 'bg-danger text-white hover:bg-danger/90',
        'ghost' => 'text-muted hover:bg-paper hover:text-ink',
    ];
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-basil disabled:cursor-not-allowed disabled:opacity-60 ' . ($variants[$variant] ?? $variants['primary'])]) }}>
    {{ $slot }}
</button>
