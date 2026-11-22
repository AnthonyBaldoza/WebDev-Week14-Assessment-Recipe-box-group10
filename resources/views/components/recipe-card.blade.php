@props(['recipe'])

<li {{ $attributes->merge(['class' => 'flex items-center gap-3 px-4 py-3.5 sm:gap-4 sm:px-5']) }}>
    <button type="button"
            wire:click="toggleFavourite({{ $recipe->id }})"
            aria-pressed="{{ $recipe->is_favourite ? 'true' : 'false' }}"
            aria-label="{{ $recipe->is_favourite ? 'Remove ' . $recipe->title . ' from favourites' : 'Add ' . $recipe->title . ' to favourites' }}"
            class="{{ $recipe->is_favourite ? 'text-saffron' : 'text-muted hover:text-saffron' }} -m-1 shrink-0 rounded-md p-1 transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-basil">
        <svg class="size-6" viewBox="0 0 24 24" fill="{{ $recipe->is_favourite ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round" aria-hidden="true">
            <path d="M11.48 3.5a.56.56 0 0 1 1.04 0l2.13 5.11a.56.56 0 0 0 .48.35l5.52.44c.5.04.7.67.32.99l-4.2 3.6a.56.56 0 0 0-.18.56l1.28 5.38a.56.56 0 0 1-.84.61l-4.73-2.89a.56.56 0 0 0-.58 0l-4.73 2.89a.56.56 0 0 1-.84-.61l1.28-5.38a.56.56 0 0 0-.18-.56l-4.2-3.6a.56.56 0 0 1 .32-.99l5.52-.44a.56.56 0 0 0 .48-.35l2.13-5.11Z"/>
        </svg>
    </button>

    <div class="min-w-0 flex-1">
        <p class="truncate font-medium">{{ $recipe->title }}</p>
        <p class="text-sm text-muted">Added {{ $recipe->created_at->diffForHumans() }}</p>
    </div>

    <x-badge tone="basil">{{ $recipe->minutes }} min</x-badge>
</li>