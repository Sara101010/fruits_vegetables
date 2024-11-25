<?php

namespace App\Entity;

class FruitsCollection extends FoodCollection
{
    protected function isValidType(object $item): bool
    {
        return $item instanceof Fruit;
    }
}