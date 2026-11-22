<?php

namespace App\Livewire;

use App\Models\Recipe;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class RecipeBox extends Component
{
    public string $title = '';
    public string $minutes = '';
    public string $search = '';

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:100'],
            'minutes' => ['required', 'integer', 'min:1', 'max:1440'],
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'Enter a title for the recipe.',
            'title.min' => 'The title needs at least :min characters.',
            'title.max' => 'The title can have at most :max characters.',
            'minutes.required' => 'Enter the cooking time in minutes.',
            'minutes.integer' => 'Use a whole number of minutes, such as 45.',
            'minutes.min' => 'Cooking time must be at least 1 minute.',
            'minutes.max' => 'Cooking time cannot be more than 1440 minutes (24 hours).',
        ];
    }

    public function save(): void
    {
        // Validate on the server again: this action can be called without the form.
        $data = $this->validate();

        Recipe::create([
            'title' => trim($data['title']),
            'minutes' => (int) $data['minutes'],
        ]);

        $this->reset('title', 'minutes');
    }

    public function toggleFavourite(int $id): void
    {
        Validator::make(['id' => $id], [
            'id' => ['required', 'integer', 'exists:recipes,id'],
        ])->validate();

        $recipe = Recipe::findOrFail($id);
        $recipe->update(['is_favourite' => ! $recipe->is_favourite]);
    }

    public function render()
    {
        // One aggregate query for both counts instead of two separate ones.
        $counts = Recipe::query()
            ->toBase()
            ->selectRaw('count(*) as total, count(*) filter (where is_favourite) as favourites')
            ->first();

        return view('livewire.recipe-box', [
            'recipes' => Recipe::search($this->search)->latestFirst()->get(),
            'totalCount' => (int) $counts->total,
            'favouriteCount' => (int) $counts->favourites,
        ]);
    }
}