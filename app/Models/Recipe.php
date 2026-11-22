<?php

namespace App\Models;

use App\Enums\Category;
use App\Enums\Difficulty;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = [
        'title', 'description', 'category', 'difficulty', 'minutes',
        'ingredients', 'steps', 'image_path', 'is_favourite',
    ];

    protected function casts(): array
    {
        return [
            'minutes' => 'integer',
            'is_favourite' => 'boolean',
            'category' => Category::class,
            'difficulty' => Difficulty::class,
            'ingredients' => 'array',
            'steps' => 'array',
        ];
    }

    /** Relative to the current host, so it works on localhost and 127.0.0.1. */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->image_path ? asset('storage/' . $this->image_path) : null
        );
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = trim($term);

        if ($term === '') {
            return $query;
        }

        // Escape LIKE wildcards so "%" and "_" typed by the user are plain text.
        $like = '%' . addcslashes($term, '%_\\') . '%';

        return $query->where(function (Builder $q) use ($like) {
            $q->where('title', 'ilike', $like)
                ->orWhere('description', 'ilike', $like)
                ->orWhereRaw('ingredients::text ilike ?', [$like]);
        });
    }

    public function scopeSortedBy(Builder $query, string $sort): Builder
    {
        return match ($sort) {
            'oldest' => $query->orderBy('created_at')->orderBy('id'),
            'title' => $query->orderByRaw('lower(title)')->orderBy('id'),
            'quickest' => $query->orderBy('minutes')->orderByDesc('id'),
            'favourites' => $query->orderByDesc('is_favourite')->orderByDesc('created_at')->orderByDesc('id'),
            default => $query->orderByDesc('created_at')->orderByDesc('id'),
        };
    }
}