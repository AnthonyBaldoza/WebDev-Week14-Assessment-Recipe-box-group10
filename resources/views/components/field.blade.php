@props(['name', 'label'])

<div>
    <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium">{{ $label }}</label>

    <input id="{{ $name }}" name="{{ $name }}"
           @if ($errors->has($name)) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
           {{ $attributes->merge(['class' => 'block w-full rounded-lg border bg-surface px-3 py-2 placeholder:text-muted/70 focus:outline-2 ' . ($errors->has($name) ? 'border-danger focus:outline-danger' : 'border-line focus:border-basil focus:outline-basil')]) }}>

    @error($name)
        <p id="{{ $name }}-error" class="mt-1.5 text-sm text-danger">{{ $message }}</p>
    @enderror
</div>