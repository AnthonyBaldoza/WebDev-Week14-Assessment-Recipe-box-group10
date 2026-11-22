@props(['tone' => 'slate'])

@php
    // Full class names so Tailwind can see them. Never build them from pieces.
    $tones = [
        'slate' => 'bg-slate-100 text-slate-700',
        'amber' => 'bg-amber-100 text-amber-800',
        'indigo' => 'bg-indigo-100 text-indigo-800',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-3 py-1 text-sm font-medium ' . ($tones[$tone] ?? $tones['slate'])]) }}>
    {{ $slot }}
</span>