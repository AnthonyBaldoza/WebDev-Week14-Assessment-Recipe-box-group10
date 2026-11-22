<?php

namespace App\Enums;

enum Category: string
{
    case Breakfast = 'breakfast';
    case Main = 'main';
    case Soup = 'soup';
    case Snack = 'snack';
    case Dessert = 'dessert';
    case Drink = 'drink';

    public function label(): string
    {
        return match ($this) {
            self::Breakfast => 'Breakfast',
            self::Main => 'Main course',
            self::Soup => 'Soup',
            self::Snack => 'Snack',
            self::Dessert => 'Dessert',
            self::Drink => 'Drink',
        };
    }
}