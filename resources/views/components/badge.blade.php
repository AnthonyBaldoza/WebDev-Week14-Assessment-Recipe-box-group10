@props(['tone' => 'neutral'])

@php
    // Full class names so Tailwind can see them. Never build them from pieces.
    $tones = [
        'neutral' => 'bg-paper text-muted ring-1 ring-inset ring-line',
        'basil' => 'bg-basil-tint text-basil-dark',
        'saffron' => 'bg-saffron-tint text-saffron-ink',
        'danger' => 'bg-danger/10 text-danger',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-sm font-medium tabular-nums ' . ($tones[$tone] ?? $tones['neutral'])]) }}>
    {{ $slot }}
</span>