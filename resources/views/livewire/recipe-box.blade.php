<div>
    <header class="sticky top-0 z-30 border-b border-line bg-paper/90 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
            <p class="font-display text-xl font-bold tracking-tight">Recipe Box</p>
            <x-button wire:click="create">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                New recipe
            </x-button>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="font-display text-3xl font-bold tracking-tight sm:text-4xl">Your recipes</h1>
                <p class="mt-1 text-muted">Everything you cook, with ingredients and steps in one place.</p>
            </div>

            <x-badge tone="saffron">
                <svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M11.48 3.5a.56.56 0 0 1 1.04 0l2.13 5.11a.56.56 0 0 0 .48.35l5.52.44c.5.04.7.67.32.99l-4.2 3.6a.56.56 0 0 0-.18.56l1.28 5.38a.56.56 0 0 1-.84.61l-4.73-2.89a.56.56 0 0 0-.58 0l-4.73 2.89a.56.56 0 0 1-.84-.61l1.28-5.38a.56.56 0 0 0-.18-.56l-4.2-3.6a.56.56 0 0 1 .32-.99l5.52-.44a.56.56 0 0 0 .48-.35l2.13-5.11Z"/></svg>
                {{ $favouriteCount }} {{ Str::plural('favourite', $favouriteCount) }}
            </x-badge>
        </div>

        {{-- Toolbar --}}
        <div class="mt-6 flex flex-col gap-3 lg:flex-row lg:items-center">
            <div class="relative lg:flex-1">
                <label for="search" class="sr-only">Search recipes</label>
                <svg class="pointer-events-none absolute left-3 top-1/2 size-5 -translate-y-1/2 text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/></svg>
                <input id="search" type="search" wire:model.live.debounce.300ms="search"
                       placeholder="Search title, description or ingredient" autocomplete="off"
                       class="block w-full rounded-lg border border-line bg-surface py-2.5 pl-10 pr-24 placeholder:text-muted/70 hover:border-muted focus:border-basil focus:outline-2 focus:outline-basil">
                <span wire:loading wire:target="search" class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-muted">Searching…</span>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:flex lg:items-center">
                <x-field name="category-filter" label="Category" :sr-label="true" type="select" wire:model.live="category">
                    <option value="">All categories</option>
                    @foreach ($categories as $option)
                        <option value="{{ $option->value }}">{{ $option->label() }}</option>
                    @endforeach
                </x-field>

                <x-field name="difficulty-filter" label="Difficulty" :sr-label="true" type="select" wire:model.live="difficulty">
                    <option value="">Any difficulty</option>
                    @foreach ($difficulties as $option)
                        <option value="{{ $option->value }}">{{ $option->label() }}</option>
                    @endforeach
                </x-field>

                <x-field name="sort" label="Sort by" :sr-label="true" type="select" wire:model.live="sort">
                    @foreach ($sorts as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-field>

                <button type="button" wire:click="$toggle('favouritesOnly')"
                        aria-pressed="{{ $favouritesOnly ? 'true' : 'false' }}"
                        class="{{ $favouritesOnly ? 'border-saffron bg-saffron-tint text-saffron-ink' : 'border-line bg-surface hover:bg-paper' }} inline-flex items-center justify-center gap-2 rounded-lg border px-3 py-2.5 text-sm font-medium transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-basil">
                    <svg class="size-4" viewBox="0 0 24 24" fill="{{ $favouritesOnly ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round" aria-hidden="true"><path d="M11.48 3.5a.56.56 0 0 1 1.04 0l2.13 5.11a.56.56 0 0 0 .48.35l5.52.44c.5.04.7.67.32.99l-4.2 3.6a.56.56 0 0 0-.18.56l1.28 5.38a.56.56 0 0 1-.84.61l-4.73-2.89a.56.56 0 0 0-.58 0l-4.73 2.89a.56.56 0 0 1-.84-.61l1.28-5.38a.56.56 0 0 0-.18-.56l-4.2-3.6a.56.56 0 0 1 .32-.99l5.52-.44a.56.56 0 0 0 .48-.35l2.13-5.11Z"/></svg>
                    Favourites
                </button>
            </div>
        </div>

        <div class="mt-4 flex min-h-6 items-center justify-between gap-3 text-sm text-muted" aria-live="polite">
            @if ($recipes->total() > 0)
                <p>Showing {{ $recipes->firstItem() }} to {{ $recipes->lastItem() }} of {{ $recipes->total() }} {{ Str::plural('recipe', $recipes->total()) }}</p>
            @else
                <p>No recipes to show</p>
            @endif

            @if ($hasFilters)
                <button type="button" wire:click="clearFilters" class="font-medium text-basil underline underline-offset-2 hover:no-underline">Clear filters</button>
            @endif
        </div>

        {{-- Results --}}
        <div wire:loading.class="opacity-60" wire:target="search,category,difficulty,sort,favouritesOnly,gotoPage,nextPage,previousPage"
             class="mt-3 transition-opacity">
            @if ($recipes->isNotEmpty())
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($recipes as $recipe)
                        <x-recipe-card :recipe="$recipe" wire:key="recipe-{{ $recipe->id }}" />
                    @endforeach
                </div>

                <div class="mt-8">{{ $recipes->links() }}</div>
            @elseif ($totalCount === 0)
                <div role="status" class="rounded-xl border border-dashed border-line px-6 py-16 text-center">
                    <svg class="mx-auto size-10 text-basil/60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/></svg>
                    <p class="mt-4 text-lg font-semibold">Your recipe box is empty</p>
                    <p class="mx-auto mt-1 max-w-sm text-sm text-muted">Add a recipe with its ingredients and steps, then find it again by name, category or ingredient.</p>
                    <x-button wire:click="create" class="mt-6">Add your first recipe</x-button>
                </div>
            @else
                <div role="status" class="rounded-xl border border-dashed border-line px-6 py-16 text-center">
                    <p class="text-lg font-semibold">No recipes match your search</p>
                    <p class="mx-auto mt-1 max-w-sm text-sm text-muted">Check the spelling, try a shorter word, or remove a filter.</p>
                    <x-button variant="secondary" wire:click="clearFilters" class="mt-6">Clear filters</x-button>
                </div>
            @endif
        </div>
    </main>

    {{-- Recipe details --}}
    @if ($viewed)
        <x-modal close="closeView" :label="$viewed->title" size="lg">
            <button type="button" wire:click="closeView" aria-label="Close"
                    class="absolute right-3 top-3 z-10 rounded-full bg-surface/90 p-2 text-muted shadow-sm hover:text-ink focus-visible:outline-2 focus-visible:outline-basil">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
            </button>

            <div class="overflow-y-auto">
                @if ($viewed->image_url)
                    <img src="{{ $viewed->image_url }}" alt="Photo of {{ $viewed->title }}" class="aspect-[16/9] w-full object-cover">
                @endif

                <div class="p-5 sm:p-7">
                    <div class="flex flex-wrap items-center gap-2">
                        <x-badge>{{ $viewed->category->label() }}</x-badge>
                        <x-badge :tone="$viewed->difficulty->tone()">{{ $viewed->difficulty->label() }}</x-badge>
                        <x-badge tone="basil">{{ $viewed->minutes }} min</x-badge>
                    </div>

                    <h2 class="mt-3 font-display text-2xl font-bold tracking-tight sm:text-3xl">{{ $viewed->title }}</h2>

                    @if ($viewed->description)
                        <p class="mt-2 max-w-prose text-muted">{{ $viewed->description }}</p>
                    @endif

                    <div class="mt-7 grid gap-8 md:grid-cols-[15rem_minmax(0,1fr)]">
                        <section>
                            <h3 class="font-semibold">Ingredients</h3>
                            <ul class="mt-3 divide-y divide-line text-sm">
                                @foreach ($viewed->ingredients ?? [] as $item)
                                    <li class="py-2">{{ $item }}</li>
                                @endforeach
                            </ul>
                        </section>

                        <section>
                            <h3 class="font-semibold">Method</h3>
                            <ol class="mt-3 space-y-4">
                                @foreach ($viewed->steps ?? [] as $number => $step)
                                    <li class="flex gap-3">
                                        <span class="flex size-7 shrink-0 items-center justify-center rounded-full bg-basil-tint text-sm font-semibold text-basil-dark">{{ $number + 1 }}</span>
                                        <p class="pt-0.5">{{ $step }}</p>
                                    </li>
                                @endforeach
                            </ol>
                        </section>
                    </div>
                </div>
            </div>

            <footer class="flex flex-wrap items-center justify-between gap-3 border-t border-line bg-paper px-5 py-4 sm:px-7">
                <x-button variant="secondary" wire:click="toggleFavourite({{ $viewed->id }})">
                    {{ $viewed->is_favourite ? 'Remove from favourites' : 'Add to favourites' }}
                </x-button>
                <div class="flex items-center gap-2">
                    <x-button variant="ghost" wire:click="confirmDelete({{ $viewed->id }})">Delete</x-button>
                    <x-button wire:click="edit({{ $viewed->id }})">Edit recipe</x-button>
                </div>
            </footer>
        </x-modal>
    @endif

    {{-- Delete confirmation --}}
    @if ($deletingRecipe)
        <x-modal close="cancelDelete" label="Delete recipe" size="md">
            <div class="p-6">
                <h2 class="text-lg font-semibold">Delete “{{ $deletingRecipe->title }}”?</h2>
                <p class="mt-2 text-sm text-muted">This permanently removes the recipe and its photo. You can’t undo this.</p>
            </div>
            <div class="flex justify-end gap-3 border-t border-line bg-paper px-6 py-4">
                <x-button variant="secondary" wire:click="cancelDelete">Keep recipe</x-button>
                <x-button variant="danger" wire:click="delete" wire:loading.attr="disabled" wire:target="delete">
                    <x-spinner wire:loading wire:target="delete" />
                    Delete recipe
                </x-button>
            </div>
        </x-modal>
    @endif

    <livewire:recipe-form />
</div>