@props(['name', 'label' => null, 'type' => 'text', 'hint' => null, 'srLabel' => false])

@php
    $invalid = $errors->has($name);

    $control = 'block w-full rounded-lg border bg-surface px-3 py-2.5 placeholder:text-muted/70 focus:outline-2 '
        . ($invalid
            ? 'border-danger focus:outline-danger'
            : 'border-line hover:border-muted focus:border-basil focus:outline-basil');

    $shared = [
        'id' => $name,
        'name' => $name,
        'aria-invalid' => $invalid ? 'true' : null,
        'aria-describedby' => $invalid ? $name . '-error' : ($hint ? $name . '-hint' : null),
    ];
@endphp

<div>
    @if ($label)
        <label for="{{ $name }}" class="{{ $srLabel ? 'sr-only' : 'mb-1.5 block text-sm font-medium' }}">{{ $label }}</label>
    @endif

    @if ($type === 'textarea')
        <textarea {{ $attributes->merge($shared + ['class' => $control]) }}></textarea>
    @elseif ($type === 'select')
        <div class="relative">
            <select {{ $attributes->merge($shared + ['class' => $control . 'appearance-none pr-9']) }}>{{ $slot }}</select>
            <svg class="pointer-events-none absolute right-3 top-1/2 size-4 -translate-y-1/2 text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
        </div>
    @else
        <input type="{{ $type }}" {{ $attributes->merge($shared + ['class' => $control]) }}>
    @endif

    @if ($invalid)
        <p id="{{ $name }}-error" class="mt-1.5 text-sm text-danger">{{ $errors->first($name) }}</p>
    @elseif ($hint)
        <p id="{{ $name }}-hint" class="mt-1.5 text-sm text-muted">{{ $hint }}</p>
    @endif
</div>