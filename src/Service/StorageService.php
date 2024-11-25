<?php

namespace App\Service;

use App\Entity\Fruit;
use App\Entity\FruitsCollection;
use App\Entity\Vegetable;
use App\Entity\VegetablesCollection;
use App\Repository\FruitRepositoryInterface;
use App\Repository\VegetableRepositoryInterface;
use DateTime;

class StorageService
{
    public function __construct(
        protected FruitRepositoryInterface $fruitRepository,
        protected VegetableRepositoryInterface $vegetableRepository
    )
    {
    }

    public function storeRequest(array $request): array
    {
        $fruitsCollection = new FruitsCollection();
        $vegetablesCollection = new VegetablesCollection();
        $skippedIds = [];

        $this->fillCollections($request, $fruitsCollection, $vegetablesCollection);
        $this->persistCollections($fruitsCollection, $vegetablesCollection, $skippedIds);

        return $skippedIds;
    }

    private function fillCollections(array $arrayRequest, FruitsCollection $fruitsCollection, VegetablesCollection $vegetablesCollection): void
    {
        foreach ($arrayRequest as $foodItem) {
            $this->addToCollectionByType($foodItem, $fruitsCollection, $vegetablesCollection);
        }
    }

    private function addToCollectionByType(array $foodItem, FruitsCollection $fruitCollection, VegetablesCollection $vegetableCollection): void
    {
        if ($foodItem['type'] === 'fruit') {
            $fruitCollection->add(new Fruit(
                $foodItem['id'],
                $foodItem['name'],
                UnitConverterService::quantityInGrams($foodItem['quantity'], $foodItem['unit']),
                new \DateTime(),
                new DateTime(),
            ));
        } elseif ($foodItem['type'] === 'vegetable') {
            $vegetableCollection->add(new Vegetable(
                $foodItem['id'],
                $foodItem['name'],
                UnitConverterService::quantityInGrams($foodItem['quantity'], $foodItem['unit']),
                new \DateTime(),
                new DateTime(),
            ));
        }
    }

    private function persistCollections(FruitsCollection $fruitsCollection, VegetablesCollection $vegetablesCollection, array &$skippedIds): void
    {
        $this->persistFruitCollection($fruitsCollection, $skippedIds);
        $this->persistVegetableCollection($vegetablesCollection, $skippedIds);
    }

    private function persistFruitCollection(FruitsCollection $fruitsCollection, array &$skippedIds): void
    {
        foreach ($fruitsCollection->list() as $fruit) {
            if ($this->fruitRepository->findById($fruit->getId())) {
                $skippedIds[] = $fruit->getId();
                continue;
            }
            $this->fruitRepository->save($fruit);
        }
    }

    private function persistVegetableCollection(VegetablesCollection $vegetablesCollection, array &$skippedIds): void
    {
        foreach ($vegetablesCollection->list() as $vegetable) {
            if ($this->vegetableRepository->findById($vegetable->getId())) {
                $skippedIds[] = $vegetable->getId();
                continue;
            }
            $this->vegetableRepository->save($vegetable);
        }
    }
}
