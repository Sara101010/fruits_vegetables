<?php

namespace App\Repository;

use App\Entity\Vegetable;
use App\Entity\VegetablesCollection;

interface VegetableRepositoryInterface
{
    public function findById(int $id): ?Vegetable;
    public function save(Vegetable $food): void;
    public function findByCriteria(FoodCriteria $criteria): VegetablesCollection;
}