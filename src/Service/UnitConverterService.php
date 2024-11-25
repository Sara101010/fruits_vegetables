<?php

namespace App\Service;

class UnitConverterService
{
    const GRAMS = 'g';
    const KILOGRAMS = 'kg';

    public static function quantityInGrams(int $quantity, string $unit): string
    {
        return match ($unit) {
            self::GRAMS => $quantity,
            self::KILOGRAMS => $quantity * 1000
        };
    }

    public static function fromGramsToKilograms(int $quantity): string
    {
        return round($quantity / 1000, 2);
    }
}