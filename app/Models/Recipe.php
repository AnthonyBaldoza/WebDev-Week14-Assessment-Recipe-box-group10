<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = ['title', 'minutes', 'is_favourite'];

    protected function casts(): array
    {
        return [
            'minutes' => 'integer',
            'is_favourite' => 'boolean',
        ];
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = trim($term);

        if ($term === '') {
            return $query;
        }

        // Escape LIKE wildcards so "%" or "_" typed by the user are treated as plain text.
        return $query->where('title', 'ilike', '%' . addcslashes($term, '%_\\') . '%');
    }

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('created_at')->orderByDesc('id');
    }
}