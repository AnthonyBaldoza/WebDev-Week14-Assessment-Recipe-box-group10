@props(['recipe'])

<article {{ $attributes->merge(['class' => 'group relative flex flex-col overflow-hidden rounded-xl border border-line bg-surface transition-[border-color,box-shadow] hover:border-muted/40 hover:shadow-md motion-reduce:transition-none']) }}>
    <div class="relative aspect-[4/3] overflow-hidden bg-basil-tint">
        @if ($recipe->image_url)
            <img src="{{ $recipe->image_url }}" alt="" loading="lazy"
                 class="size-full object-cover transition-transform duration-500 group-hover:scale-[1.03] motion-reduce:transition-none motion-reduce:group-hover:scale-100">
        @else
            <div class="flex size-full items-center justify-center text-basil/50">
                <svg class="size-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25" aria-hidden="true"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/></svg>
            </div>
        @endif

        <button type="button"
                wire:click="toggleFavourite({{ $recipe->id }})"
                aria-pressed="{{ $recipe->is_favourite ? 'true' : 'false' }}"
                aria-label="{{ $recipe->is_favourite ? 'Remove ' . $recipe->title . ' from favourites' : 'Add ' . $recipe->title . ' to favourites' }}"
                class="{{ $recipe->is_favourite ? 'text-saffron' : 'text-muted hover:text-saffron' }} absolute right-3 top-3 z-10 rounded-full bg-surface/90 p-2 shadow-sm transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-basil">
            <svg class="size-5" viewBox="0 0 24 24" fill="{{ $recipe->is_favourite ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round" aria-hidden="true">
                <path d="M11.48 3.5a.56.56 0 0 1 1.04 0l2.13 5.11a.56.56 0 0 0 .48.35l5.52.44c.5.04.7.67.32.99l-4.2 3.6a.56.56 0 0 0-.18.56l1.28 5.38a.56.56 0 0 1-.84.61l-4.73-2.89a.56.56 0 0 0-.58 0l-4.73 2.89a.56.56 0 0 1-.84-.61l1.28-5.38a.56.56 0 0 0-.18-.56l-4.2-3.6a.56.56 0 0 1 .32-.99l5.52-.44a.56.56 0 0 0 .48-.35l2.13-5.11Z"/>
            </svg>
        </button>
    </div>

    <div class="flex flex-1 flex-col gap-3 p-4">
        <div>
            <p class="text-sm text-muted">{{ $recipe->category->label() }}</p>
            <h3 class="mt-0.5 text-lg font-semibold leading-snug">
                <button type="button" wire:click="view({{ $recipe->id }})"
                        class="text-left after:absolute after:inset-0 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-basil">{{ $recipe->title }}</button>
            </h3>
            @if ($recipe->description)
                <p class="mt-1 line-clamp-2 text-sm text-muted">{{ $recipe->description }}</p>
            @endif
        </div>

        <div class="mt-auto flex flex-wrap items-center gap-2">
            <x-badge>{{ $recipe->minutes }} min</x-badge>
            <x-badge :tone="$recipe->difficulty->tone()">{{ $recipe->difficulty->label() }}</x-badge>
        </div>
    </div>
</article>