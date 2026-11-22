<?php

namespace App\Livewire;

use App\Enums\Category;
use App\Enums\Difficulty;
use App\Models\Recipe;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class RecipeBox extends Component
{
    use WithPagination;

    public const SORTS = [
        'newest' => 'Newest first',
        'oldest' => 'Oldest first',
        'title' => 'Title, A to Z',
        'quickest' => 'Quickest first',
        'favourites' => 'Favourites first',
    ];

    private const PER_PAGE = 9;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $category = '';

    #[Url]
    public string $difficulty = '';

    #[Url]
    public string $sort = 'newest';

    #[Url(as: 'fav')]
    public bool $favouritesOnly = false;

    public ?int $viewing = null;
    public ?int $deleting = null;

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'category', 'difficulty', 'sort', 'favouritesOnly'], true)) {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'category', 'difficulty', 'favouritesOnly');
        $this->resetPage();
    }

    public function create(): void
    {
        $this->dispatch('open-recipe-form');
    }

    public function edit(int $id): void
    {
        $recipe = $this->resolve($id);

        if (! $recipe) {
            return;
        }

        $this->viewing = null;
        $this->dispatch('open-recipe-form', id: $recipe->id);
    }

    public function view(int $id): void
    {
        $recipe = $this->resolve($id);

        if ($recipe) {
            $this->viewing = $recipe->id;
        }
    }

    public function closeView(): void
    {
        $this->viewing = null;
    }

    public function toggleFavourite(int $id): void
    {
        $recipe = $this->resolve($id);

        if (! $recipe) {
            return;
        }

        $recipe->update(['is_favourite' => ! $recipe->is_favourite]);

        $this->notify('success', $recipe->is_favourite ? 'Added to favourites.' : 'Removed from favourites.');
    }

    public function confirmDelete(int $id): void
    {
        $recipe = $this->resolve($id);

        if ($recipe) {
            $this->viewing = null;
            $this->deleting = $recipe->id;
        }
    }

    public function cancelDelete(): void
    {
        $this->deleting = null;
    }

    public function delete(): void
    {
        $recipe = $this->deleting ? $this->resolve($this->deleting) : null;
        $this->deleting = null;

        if (! $recipe) {
            return;
        }

        $image = $recipe->image_path;
        $recipe->delete();

        if ($image) {
            Storage::disk('public')->delete($image);
        }

        $this->resetPage();
        $this->notify('success', 'Recipe deleted.');
    }

    /** The form saved something: re-render the list. */
    #[On('recipe-saved')]
    public function refresh(): void
    {
        //
    }

    public function render()
    {
        $search = mb_substr(trim($this->search), 0, 100);
        $category = Category::tryFrom($this->category);
        $difficulty = Difficulty::tryFrom($this->difficulty);
        $sort = array_key_exists($this->sort, self::SORTS) ? $this->sort : 'newest';

        $recipes = Recipe::query()
            ->search($search)
            ->when($category, fn ($q, Category $c) => $q->where('category', $c->value))
            ->when($difficulty, fn ($q, Difficulty $d) => $q->where('difficulty', $d->value))
            ->when($this->favouritesOnly, fn ($q) => $q->where('is_favourite', true))
            ->sortedBy($sort)
            ->paginate(self::PER_PAGE);

        $counts = Recipe::query()
            ->toBase()
            ->selectRaw('count(*) as total, count(*) filter (where is_favourite) as favourites')
            ->first();

        $viewed = $this->viewing ? Recipe::find($this->viewing) : null;

        if ($this->viewing && ! $viewed) {
            $this->viewing = null;
        }

        return view('livewire.recipe-box', [
            'recipes' => $recipes,
            'totalCount' => (int) $counts->total,
            'favouriteCount' => (int) $counts->favourites,
            'viewed' => $viewed,
            'deletingRecipe' => $this->deleting ? Recipe::find($this->deleting) : null,
            'categories' => Category::cases(),
            'difficulties' => Difficulty::cases(),
            'sorts' => self::SORTS,
            'hasFilters' => $search !== '' || $category || $difficulty || $this->favouritesOnly,
        ]);
    }

    /** Every action validates its id, even though the UI only sends real ones. */
    private function resolve(int $id): ?Recipe
    {
        $valid = Validator::make(['id' => $id], [
            'id' => ['required', 'integer', 'exists:recipes,id'],
        ])->passes();

        $recipe = $valid ? Recipe::find($id) : null;

        if (! $recipe) {
            $this->notify('error', 'That recipe no longer exists.');
        }

        return $recipe;
    }

    private function notify(string $type, string $message): void
    {
        $this->dispatch('notify', type: $type, message: $message);
    }
}