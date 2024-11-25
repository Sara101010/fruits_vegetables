<?php

namespace App\Tests\App\Service;

use App\Entity\Fruit;
use App\Entity\FruitsCollection;
use App\Entity\Vegetable;
use App\Entity\VegetablesCollection;
use App\Repository\FruitRepositoryInterface;
use App\Repository\VegetableRepositoryInterface;
use App\Service\ListService;
use App\ValueObject\FoodType;
use DateTime;
use PHPUnit\Framework\TestCase;

class ListServiceTest extends TestCase
{    protected function setUp(): void
    {
        $this->fruitRepositoryMock = $this->createMock(FruitRepositoryInterface::class);
        $this->vegetableRepositoryMock = $this->createMock(VegetableRepositoryInterface::class);

        $this->listService = new ListService(
            $this->fruitRepositoryMock,
            $this->vegetableRepositoryMock
        );
    }

    public function testGetListWithNoTypeFilters(): void
    {
        $mockedFruits = $this->getFruits();
        $mockedVegetables = $this->getVegetables();

        $this->fruitRepositoryMock->method('findByCriteria')->willReturn($mockedFruits);
        $this->vegetableRepositoryMock->method('findByCriteria')->willReturn($mockedVegetables);

        $result = $this->listService->getList([]);

        $this->assertArrayHasKey('fruits', $result);
        $this->assertArrayHasKey('vegetables', $result);
    }

    public function testGetListWithUnitKg(): void
    {
        $mockedFruits = $this->getFruits();
        $mockedVegetables = $this->getVegetables();

        $this->fruitRepositoryMock->method('findByCriteria')->willReturn($mockedFruits);
        $this->vegetableRepositoryMock->method('findByCriteria')->willReturn($mockedVegetables);

        $result = $this->listService->getList(['unit' => 'kg']);

        $this->assertEquals(1.0, $result['fruits'][0]['quantity']);
        $this->assertEquals(0.5, $result['vegetables'][0]['quantity']);
        $this->assertEquals('kg', $result['fruits'][0]['unit']);
        $this->assertEquals('kg', $result['vegetables'][0]['unit']);
    }

    public function testGetListWithSpecificType(): void
    {
        $mockedFruits = $this->getFruits();

        $this->fruitRepositoryMock->method('findByCriteria')->willReturn($mockedFruits);

        $result = $this->listService->getList(['type' => FoodType::Fruit->value]);

        $this->assertArrayHasKey('fruits', $result);
        $this->assertArrayNotHasKey('vegetables', $result);
    }

    private function getFruits(): FruitsCollection
    {
        return new FruitsCollection(
            [
                new Fruit(1, 'Apple', 1000, new \DateTime(), new DateTime()),
            ]
        );
    }

    private function getVegetables(): VegetablesCollection
    {
        return new VegetablesCollection(
            [
                new Vegetable(2, 'Carrot', 500, new \DateTime(), new DateTime()),
            ]
        );
    }
}
