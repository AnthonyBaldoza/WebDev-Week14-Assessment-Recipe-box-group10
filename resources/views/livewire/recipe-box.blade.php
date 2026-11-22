<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8 lg:py-14">

    <header class="flex flex-wrap items-end justify-between gap-4 border-b border-line pb-6">
        <div>
            <h1 class="font-display text-3xl font-bold tracking-tight sm:text-4xl">Recipe Box</h1>
            <p class="mt-1 text-muted">Save what you cook and find it again in seconds.</p>
        </div>

        <x-badge tone="saffron">
            <svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M11.48 3.5a.56.56 0 0 1 1.04 0l2.13 5.11a.56.56 0 0 0 .48.35l5.52.44c.5.04.7.67.32.99l-4.2 3.6a.56.56 0 0 0-.18.56l1.28 5.38a.56.56 0 0 1-.84.61l-4.73-2.89a.56.56 0 0 0-.58 0l-4.73 2.89a.56.56 0 0 1-.84-.61l1.28-5.38a.56.56 0 0 0-.18-.56l-4.2-3.6a.56.56 0 0 1 .32-.99l5.52-.44a.56.56 0 0 0 .48-.35l2.13-5.11Z"/>
            </svg>
            {{ $favouriteCount }} {{ Str::plural('favourite', $favouriteCount) }}
        </x-badge>
    </header>

    <div class="mt-8 grid gap-8 lg:grid-cols-[20rem_minmax(0,1fr)] lg:items-start">

        {{-- Add form --}}
        <form wire:submit="save" novalidate
              class="rounded-lg border border-line bg-surface p-5 lg:sticky lg:top-8">
            <h2 class="text-lg font-semibold">Add a recipe</h2>

            <div class="mt-4 space-y-4">
                <x-field name="title" label="Title" type="text"
                         placeholder="Chicken adobo" autocomplete="off"
                         wire:model="title" />

                <x-field name="minutes" label="Cooking time (minutes)" type="number"
                         inputmode="numeric" placeholder="45"
                         wire:model="minutes" />
            </div>

            <button type="submit" wire:loading.attr="disabled" wire:target="save"
                    class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-basil px-4 py-2.5 font-semibold text-white transition-colors hover:bg-basil-dark focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-basil disabled:cursor-not-allowed disabled:opacity-60">
                <svg wire:loading wire:target="save" class="size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-opacity="0.3" stroke-width="3"/>
                    <path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                </svg>
                <span wire:loading.remove wire:target="save">Add recipe</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>
        </form>

        {{-- Search and list --}}
        <section aria-labelledby="recipes-heading" class="min-w-0">
            <h2 id="recipes-heading" class="sr-only">Your recipes</h2>

            <div class="relative">
                <label for="search" class="sr-only">Search recipes</label>
                <svg class="pointer-events-none absolute left-3 top-1/2 size-5 -translate-y-1/2 text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/>
                </svg>
                <input id="search" type="search" wire:model.live.debounce.300ms="search"
                       placeholder="Search by title"
                       autocomplete="off"
                       class="block w-full rounded-lg border border-line bg-surface py-2.5 pl-10 pr-28 placeholder:text-muted/70 focus:border-basil focus:outline-2 focus:outline-basil">
                <span wire:loading wire:target="search"
                      class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-muted">Searching…</span>
            </div>

            <p class="mt-4 text-sm text-muted" aria-live="polite">
                Showing {{ $recipes->count() }} of {{ $totalCount }} {{ Str::plural('recipe', $totalCount) }}
            </p>

            <div wire:loading.class="opacity-60" wire:target="search,toggleFavourite"
                 class="mt-2 transition-opacity">
                @if ($recipes->isNotEmpty())
                    <ul class="divide-y divide-line overflow-hidden rounded-lg border border-line bg-surface">
                        @foreach ($recipes as $recipe)
                            <x-recipe-card :recipe="$recipe" wire:key="recipe-{{ $recipe->id }}" />
                        @endforeach
                    </ul>
                @else
                    <div role="status" class="rounded-lg border border-dashed border-line px-6 py-12 text-center">
                        @if ($totalCount === 0)
                            <p class="font-medium">No recipes yet</p>
                            <p class="mt-1 text-sm text-muted">Add your first one using the form.</p>
                        @else
                            <p class="font-medium">No recipes match “{{ $search }}”</p>
                            <p class="mt-1 text-sm text-muted">Check the spelling or try a shorter word.</p>
                        @endif
                    </div>
                @endif
            </div>
        </section>
    </div>
</div>