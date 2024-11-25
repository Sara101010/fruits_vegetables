<?php

namespace App\Service;


use App\Repository\FoodCriteria;
use App\Repository\FruitRepositoryInterface;
use App\Repository\VegetableRepositoryInterface;
use App\ValueObject\FoodType;

class ListService
{
    const KG = 'kg';

    public function __construct(
        protected FruitRepositoryInterface $fruitRepository,
        protected VegetableRepositoryInterface $vegetableRepository
    )
    {
    }

    public function getList(?array $query = []): array
    {
        $criteria = $this->createFoodCriteria($query);
        return $this->getItems($criteria, $query['type'] ?? null, $query['unit'] ?? null);
    }

    private function createFoodCriteria(array $filters): FoodCriteria
    {
        return new FoodCriteria(
            $filters['id'] ?? null,
            $filters['name'] ?? null,
            $filters['page'] ?? null,
            $filters['limit'] ?? null,
        );
    }

    private function getItems(FoodCriteria $criteria, ?string $type, ?string $unit): array
    {
        if (!isset($type) || $type === FoodType::Fruit->value) {
            $fruits = $this->fruitRepository->findByCriteria($criteria)->toArray();
            $items['fruits'] =  $this->adjustQuantityToRequestedUnit($fruits, $unit);
        }

        if (!isset($type) || $type === FoodType::Vegetable->value) {
            $vegetables = $this->vegetableRepository->findByCriteria($criteria)->toArray();
            $items['vegetables'] = $this->adjustQuantityToRequestedUnit($vegetables, $unit);
        }

        return $items ?? [];
    }

    private function adjustQuantityToRequestedUnit(array $foodList, ?string $unit): array
    {
        if ($unit && strtolower($unit === self::KG)) {
            foreach ($foodList as  $key => $foodItem) {
                $this->overrideQuantityAndUnit($foodList[$key]);
            }
        }

        return $foodList;
    }

    private function overrideQuantityAndUnit(?array &$item): void
    {
        if (isset($item['unit']) && isset($item['quantity'])) {
            $item['unit'] = self::KG;
            $item['quantity']  = UnitConverterService::fromGramsToKilograms($item['quantity']);
        }
    }
}