<?php

namespace App\Repository;

use App\Entity\Fruit;
use App\Entity\FruitsCollection;

interface FruitRepositoryInterface
{
    public function findById(int $id): ?Fruit;
    public function save(Fruit $food): void;
    public function findByCriteria(FoodCriteria $criteria): FruitsCollection;
}