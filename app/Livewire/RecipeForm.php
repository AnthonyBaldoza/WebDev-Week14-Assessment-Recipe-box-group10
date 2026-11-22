<?php

namespace App\Livewire;

use App\Enums\Category;
use App\Enums\Difficulty;
use App\Models\Recipe;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Throwable;

class RecipeForm extends Component
{
    use WithFileUploads;

    private const MAX_ROWS = 30;

    public bool $open = false;
    public ?int $recipeId = null;

    public string $title = '';
    public string $description = '';
    public string $category = 'main';
    public string $difficulty = 'easy';
    public string $minutes = '';

    /** @var array<int, string> */
    public array $ingredients = [''];

    /** @var array<int, string> */
    public array $steps = [''];

    public $image = null;
    public ?string $currentImage = null;
    public bool $removeImage = false;

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'category' => ['required', Rule::enum(Category::class)],
            'difficulty' => ['required', Rule::enum(Difficulty::class)],
            'minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'ingredients' => ['required', 'array', 'min:1', 'max:' . self::MAX_ROWS],
            'ingredients.*' => ['required', 'string', 'max:150'],
            'steps' => ['required', 'array', 'min:1', 'max:' . self::MAX_ROWS],
            'steps.*' => ['required', 'string', 'max:400'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'Enter a title for the recipe.',
            'title.min' => 'The title needs at least :min characters.',
            'title.max' => 'The title can have at most :max characters.',
            'description.max' => 'The description can have at most :max characters.',
            'minutes.required' => 'Enter the cooking time in minutes.',
            'minutes.integer' => 'Use a whole number of minutes, such as 45.',
            'minutes.min' => 'Cooking time must be at least 1 minute.',
            'minutes.max' => 'Cooking time cannot be more than 1440 minutes (24 hours).',
            'ingredients.*.required' => 'Fill in this ingredient or remove the row.',
            'ingredients.*.max' => 'An ingredient can have at most :max characters.',
            'steps.*.required' => 'Fill in this step or remove the row.',
            'steps.*.max' => 'A step can have at most :max characters.',
            'image.image' => 'Upload a JPG, PNG or WebP image.',
            'image.mimes' => 'Upload a JPG, PNG or WebP image.',
            'image.max' => 'The photo must be 2 MB or smaller.',
            'image.uploaded' => 'The photo could not be uploaded. Check that it is 2 MB or smaller.',
        ];
    }

    #[On('open-recipe-form')]
    public function openForm(?int $id = null): void
    {
        $this->resetForm();

        if ($id !== null) {
            $recipe = Recipe::find($id);

            if (! $recipe) {
                $this->notify('error', 'That recipe no longer exists.');

                return;
            }

            $this->recipeId = $recipe->id;
            $this->title = $recipe->title;
            $this->description = $recipe->description ?? '';
            $this->category = $recipe->category->value;
            $this->difficulty = $recipe->difficulty->value;
            $this->minutes = (string) $recipe->minutes;
            $this->ingredients = $recipe->ingredients ?: [''];
            $this->steps = $recipe->steps ?: [''];
            $this->currentImage = $recipe->image_path;
        }

        $this->open = true;
    }

    public function close(): void
    {
        $this->resetForm();
    }

    public function updatedImage(): void
    {
        try {
            $this->validateOnly('image');
        } catch (ValidationException $e) {
            $this->image = null;

            throw $e;
        }

        $this->removeImage = false;
    }

    public function addRow(string $list): void
    {
        if (! $this->isList($list) || count($this->{$list}) >= self::MAX_ROWS) {
            return;
        }

        $this->{$list}[] = '';
    }

    public function removeRow(string $list, int $index): void
    {
        if (! $this->isList($list) || count($this->{$list}) <= 1 || ! array_key_exists($index, $this->{$list})) {
            return;
        }

        unset($this->{$list}[$index]);
        $this->{$list} = array_values($this->{$list});
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->ingredients = $this->cleanList($this->ingredients);
        $this->steps = $this->cleanList($this->steps);

        // Validate on the server: this action can be called without using the form.
        $data = $this->validate();

        $recipe = $this->recipeId ? Recipe::find($this->recipeId) : new Recipe();

        if (! $recipe) {
            $this->notify('error', 'That recipe no longer exists.');
            $this->resetForm();

            return;
        }

        $payload = [
            'title' => trim($data['title']),
            'description' => filled($data['description'] ?? null) ? trim($data['description']) : null,
            'category' => $data['category'],
            'difficulty' => $data['difficulty'],
            'minutes' => (int) $data['minutes'],
            'ingredients' => $data['ingredients'],
            'steps' => $data['steps'],
        ];

        $oldImage = $recipe->image_path;
        $newImage = null;

        try {
            if ($this->image) {
                $newImage = $this->image->store('recipes', 'public');
                $payload['image_path'] = $newImage;
            } elseif ($this->removeImage) {
                $payload['image_path'] = null;
            }

            $wasNew = ! $recipe->exists;
            $recipe->fill($payload)->save();
        } catch (Throwable $e) {
            report($e);

            if ($newImage) {
                Storage::disk('public')->delete($newImage);
            }

            $this->notify('error', 'The recipe could not be saved. Please try again.');

            return;
        }

        if ($oldImage && ($newImage || $this->removeImage)) {
            Storage::disk('public')->delete($oldImage);
        }

        $this->notify('success', $wasNew ? 'Recipe created.' : 'Changes saved.');
        $this->dispatch('recipe-saved');
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.recipe-form', [
            'categories' => Category::cases(),
            'difficulties' => Difficulty::cases(),
        ]);
    }

    private function resetForm(): void
    {
        $this->reset();
        $this->resetValidation();
    }

    private function isList(string $list): bool
    {
        return in_array($list, ['ingredients', 'steps'], true);
    }

    /** Trim every row, drop the empty ones, and always keep one row for the UI. */
    private function cleanList(array $items): array
    {
        $clean = [];

        foreach ($items as $item) {
            $item = is_scalar($item) ? trim((string) $item) : '';

            if ($item !== '') {
                $clean[] = $item;
            }
        }

        return $clean === [] ? [''] : $clean;
    }

    private function notify(string $type, string $message): void
    {
        $this->dispatch('notify', type: $type, message: $message);
    }
}