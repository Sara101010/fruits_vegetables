<?php

namespace App\Tests\App\Service;

use App\Entity\Fruit;
use App\Repository\FruitRepositoryInterface;
use App\Repository\VegetableRepositoryInterface;
use App\Service\StorageService;
use DateTime;
use PHPUnit\Framework\TestCase;

class StorageServiceTest extends TestCase
{
    protected function setUp(): void
    {
        $this->fruitRepositoryMock = $this->createMock(FruitRepositoryInterface::class);
        $this->vegetableRepositoryMock = $this->createMock(VegetableRepositoryInterface::class);

        $this->storageService = new StorageService(
            $this->fruitRepositoryMock,
            $this->vegetableRepositoryMock
        );
    }

    public function testStoreRequestWithNewItems(): void
    {
        $request = $this->getValidRequest();

        $this->fruitRepositoryMock
            ->expects(self::once())
            ->method('findById')
            ->willReturn(null);
        $this->vegetableRepositoryMock
            ->expects(self::once())
            ->method('findById')
            ->willReturn(null);

        $this->fruitRepositoryMock
            ->expects(self::once())
            ->method('save');
        $this->vegetableRepositoryMock
            ->expects(self::once())
            ->method('save');

        $skippedIds = $this->storageService->storeRequest($request);

        $this->assertEmpty($skippedIds);
    }

    public function testStoreRequestWithSkippedItems(): void
    {
        $request = $this->getValidRequest();

        $this->fruitRepositoryMock
            ->expects(self::once())
            ->method('findById')
            ->willReturn(new Fruit(1, 'Apple', 1000, new DateTime(), new DateTime()));
        $this->vegetableRepositoryMock
            ->expects(self::once())
            ->method('findById')
            ->willReturn(null);

        $this->fruitRepositoryMock
            ->expects($this->never())
            ->method('save');
        $this->vegetableRepositoryMock
            ->expects($this->once())
            ->method('save');

        $this->storageService->storeRequest($request);
    }

    private function getValidRequest(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Apple',
                'type' => 'fruit',
                'quantity' => 1000,
                'unit' => 'g',
            ],
            [
                'id' => 2,
                'name' => 'Carrot',
                'type' => 'vegetable',
                'quantity' => 500,
                'unit' => 'g',
            ],
        ];
    }
}
