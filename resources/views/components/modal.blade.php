@props(['close', 'label', 'variant' => 'center', 'size' => 'lg'])

@php
    $sizes = ['md' => 'max-w-md', 'lg' => 'max-w-3xl'];

    $wrapper = $variant === 'side' ? 'justify-end' : 'items-center justify-center p-4';

    $panel = $variant === 'side'
        ? 'h-full w-full max-w-xl animate-slide-in motion-reduce:animate-none'
        : 'max-h-[calc(100dvh-2rem)] w-full rounded-xl animate-pop motion-reduce:animate-none ' . ($sizes[$size] ?? $sizes['lg']);
@endphp

<div data-modal role="dialog" aria-modal="true" aria-label="{{ $label }}"
     wire:keydown.escape.window="{{ $close }}"
     class="fixed inset-0 z-40 flex {{ $wrapper }}">
    <div wire:click="{{ $close }}" class="absolute inset-0 animate-fade-in bg-ink/50 motion-reduce:animate-none" aria-hidden="true"></div>

    <div {{ $attributes->merge(['class' => 'relative z-10 flex flex-col overflow-hidden bg-surface shadow-xl ' . $panel]) }}>
        {{ $slot }}
    </div>
</div>