<?php

namespace App\Tests\App\Repository;

use App\Entity\Fruit;
use App\Entity\FruitsCollection;
use App\Repository\FoodCriteria;
use App\Repository\FruitRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

class FruitRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->builder = $this->createMock(QueryBuilder::class);
        $classMetadata = new ClassMetadata(Fruit::class);

        $this->entityManager
            ->method('getClassMetadata')
            ->with(Fruit::class)
            ->willReturn($classMetadata);

        $this->fruitRepository = $this->getMockBuilder(FruitRepository::class)
            ->setConstructorArgs([$this->entityManager])
            ->onlyMethods(['createQueryBuilder'])
            ->getMock();

        $this->fruitRepository
            ->method('createQueryBuilder')
            ->with('f')
            ->willReturn($this->builder);
    }

    public function testFindById(): void
    {
        $fruitId = 1;
        $fruit = new Fruit($fruitId, 'Apple', 100, new \DateTime(), new \DateTime());
        $this->entityManager->expects($this->once())
            ->method('find')
            ->with(Fruit::class, $fruitId)
            ->willReturn($fruit);

        $result = $this->fruitRepository->findById($fruitId);

        $this->assertInstanceOf(Fruit::class, $result);
        $this->assertEquals($fruitId, $result->getId());
    }

    public function testSave(): void
    {
        $fruit = new Fruit(1, 'Apple', 100, new \DateTime(), new \DateTime());

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($fruit);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->fruitRepository->save($fruit);
    }

    public function testFindByCriteria(): void
    {
        $criteria = new FoodCriteria('1', 'Apple', 1, 10);

        $this->builder->expects(self::exactly(2))
            ->method('andWhere')
            ->withConsecutive(
                ['f.id = :id'],
                ['f.name LIKE :name']
            )
            ->willReturn($this->builder);

        $this->builder->expects(self::exactly(2))
            ->method('setParameter')
            ->withConsecutive(
                ['id', $criteria->id],
                ['name', '%Apple%']
            )
            ->willReturn($this->builder);

        $this->builder->expects(self::once())
            ->method('orderBy')
            ->with('f.createdAt', 'DESC')
            ->willReturn($this->builder);

        $this->builder->expects(self::once())
            ->method('setFirstResult')
            ->with(0)
            ->willReturn($this->builder);

        $this->builder->expects(self::once())
            ->method('setMaxResults')
            ->with(10)
            ->willReturn($this->builder);

        $query = $this->createMock(AbstractQuery::class);
        $query->expects(self::once())
            ->method('getResult')
            ->willReturn([new Fruit(1, 'Apple', 100, new \DateTime(), new \DateTime())]);

        $this->builder->expects(self::once())
            ->method('getQuery')
            ->willReturn($query);

        $result = $this->fruitRepository->findByCriteria($criteria);

        $this->assertInstanceOf(FruitsCollection::class, $result);
        $this->assertCount(1, $result->list());
    }
}
