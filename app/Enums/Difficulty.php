<?php

namespace App\Enums;

enum Difficulty: string
{
    case Easy = 'easy';
    case Medium = 'medium';
    case Hard = 'hard';

    public function label(): string
    {
        return match ($this) {
            self::Easy => 'Easy',
            self::Medium => 'Medium',
            self::Hard => 'Hard',
        };
    }

    /** Badge tone used by <x-badge>. */
    public function tone(): string
    {
        return match ($this) {
            self::Easy => 'basil',
            self::Medium => 'saffron',
            self::Hard => 'danger',
        };
    }
}