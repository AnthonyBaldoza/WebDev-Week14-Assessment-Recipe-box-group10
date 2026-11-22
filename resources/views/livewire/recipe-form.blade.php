<div>
    @if ($open)
        <x-modal close="close" :label="$recipeId ? 'Edit recipe' : 'New recipe'" variant="side">
            <form wire:submit="save" novalidate class="flex min-h-0 flex-1 flex-col">
                <header class="flex items-start justify-between gap-4 border-b border-line px-5 py-4 sm:px-6">
                    <div>
                        <h2 class="font-display text-xl font-bold tracking-tight">{{ $recipeId ? 'Edit recipe' : 'New recipe' }}</h2>
                        <p class="text-sm text-muted">Add the details, ingredients and steps.</p>
                    </div>
                    <button type="button" wire:click="close" aria-label="Close"
                            class="-m-1 rounded-md p-1 text-muted hover:text-ink focus-visible:outline-2 focus-visible:outline-basil">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
                    </button>
                </header>

                <div class="flex-1 space-y-6 overflow-y-auto px-5 py-5 sm:px-6">
                    <x-field name="title" label="Title" placeholder="Chicken adobo" autocomplete="off" wire:model="title" />

                    <x-field name="description" label="Description" type="textarea" rows="3"
                             hint="Optional. Up to 500 characters." wire:model="description" />

                    <div class="grid gap-4 sm:grid-cols-3">
                        <x-field name="category" label="Category" type="select" wire:model="category">
                            @foreach ($categories as $option)
                                <option value="{{ $option->value }}">{{ $option->label() }}</option>
                            @endforeach
                        </x-field>

                        <x-field name="difficulty" label="Difficulty" type="select" wire:model="difficulty">
                            @foreach ($difficulties as $option)
                                <option value="{{ $option->value }}">{{ $option->label() }}</option>
                            @endforeach
                        </x-field>

                        <x-field name="minutes" label="Minutes" type="number" inputmode="numeric" placeholder="45" wire:model="minutes" />
                    </div>

                    {{-- Photo --}}
                    <div>
                        <label for="image" class="mb-1.5 block text-sm font-medium">Photo</label>

                        @if ($image && $image->isPreviewable())
                            <img src="{{ $image->temporaryUrl() }}" alt="Preview of the selected photo" class="mb-3 aspect-[16/9] w-full rounded-lg object-cover">
                        @elseif ($currentImage && ! $removeImage)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $currentImage) }}" alt="Current photo" class="aspect-[16/9] w-full rounded-lg object-cover">
                                <button type="button" wire:click="$set('removeImage', true)"
                                        class="mt-2 text-sm font-medium text-danger underline underline-offset-2 hover:no-underline">Remove photo</button>
                            </div>
                        @endif

                        <input id="image" type="file" wire:model="image" accept="image/jpeg,image/png,image/webp"
                               class="block w-full text-sm text-muted file:mr-4 file:cursor-pointer file:rounded-lg file:border-0 file:bg-basil-tint file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-basil-dark hover:file:bg-basil-tint/70">
                        <p wire:loading wire:target="image" class="mt-1.5 text-sm text-muted">Uploading…</p>
                        <p class="mt-1.5 text-sm text-muted">JPG, PNG or WebP, up to 2 MB.</p>
                        @error('image') <p class="mt-1.5 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>

                    {{-- Ingredients --}}
                    <fieldset>
                        <legend class="mb-1.5 text-sm font-medium">Ingredients</legend>
                        <div class="space-y-2">
                            @foreach ($ingredients as $i => $ingredient)
                                <div class="flex items-start gap-2" wire:key="ingredient-{{ $i }}">
                                    <div class="flex-1">
                                        <x-field :name="'ingredients.' . $i" :aria-label="'Ingredient ' . ($i + 1)"
                                                 placeholder="e.g. 500 g chicken thighs" autocomplete="off"
                                                 x-on:keydown.enter.prevent
                                                 wire:model="ingredients.{{ $i }}" />
                                    </div>
                                    <button type="button" wire:click="removeRow('ingredients', {{ $i }})"
                                            @disabled(count($ingredients) === 1)
                                            aria-label="Remove ingredient {{ $i + 1 }}"
                                            class="mt-2 rounded-md p-1.5 text-muted hover:text-danger focus-visible:outline-2 focus-visible:outline-basil disabled:pointer-events-none disabled:opacity-40">
                                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        @if ($errors->has('ingredients'))
                            <p class="mt-1.5 text-sm text-danger">{{ $errors->first('ingredients') }}</p>
                        @endif
                        <x-button variant="ghost" wire:click="addRow('ingredients')" class="mt-2 -ml-3">Add ingredient</x-button>
                    </fieldset>

                    {{-- Steps --}}
                    <fieldset>
                        <legend class="mb-1.5 text-sm font-medium">Steps</legend>
                        <div class="space-y-3">
                            @foreach ($steps as $i => $step)
                                <div class="flex items-start gap-2" wire:key="step-{{ $i }}">
                                    <span class="mt-2 flex size-7 shrink-0 items-center justify-center rounded-full bg-basil-tint text-sm font-semibold text-basil-dark">{{ $i + 1 }}</span>
                                    <div class="flex-1">
                                        <x-field :name="'steps.' . $i" type="textarea" rows="2"
                                                 :aria-label="'Step ' . ($i + 1)"
                                                 placeholder="Describe this step"
                                                 wire:model="steps.{{ $i }}" />
                                    </div>
                                    <button type="button" wire:click="removeRow('steps', {{ $i }})"
                                            @disabled(count($steps) === 1)
                                            aria-label="Remove step {{ $i + 1 }}"
                                            class="mt-2 rounded-md p-1.5 text-muted hover:text-danger focus-visible:outline-2 focus-visible:outline-basil disabled:pointer-events-none disabled:opacity-40">
                                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        @if ($errors->has('steps'))
                            <p class="mt-1.5 text-sm text-danger">{{ $errors->first('steps') }}</p>
                        @endif
                        <x-button variant="ghost" wire:click="addRow('steps')" class="mt-2 -ml-3">Add step</x-button>
                    </fieldset>
                </div>

                <footer class="flex items-center justify-end gap-3 border-t border-line bg-paper px-5 py-4 sm:px-6">
                    <x-button variant="secondary" wire:click="close">Cancel</x-button>
                    <x-button type="submit" wire:loading.attr="disabled" wire:target="save,image">
                        <x-spinner wire:loading wire:target="save" />
                        <span wire:loading.remove wire:target="save">{{ $recipeId ? 'Save changes' : 'Create recipe' }}</span>
                        <span wire:loading wire:target="save">Saving…</span>
                    </x-button>
                </footer>
            </form>
        </x-modal>
    @endif
</div>