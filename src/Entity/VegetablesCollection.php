<?php

namespace App\Entity;

class VegetablesCollection extends FoodCollection
{
    protected function isValidType(object $item): bool
    {
        return $item instanceof Vegetable;
    }
}