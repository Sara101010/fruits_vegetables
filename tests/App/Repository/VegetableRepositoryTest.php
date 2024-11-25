<?php

namespace App\Tests\App\Repository;

use App\Entity\Vegetable;
use App\Entity\VegetablesCollection;
use App\Repository\FoodCriteria;
use App\Repository\VegetableRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

class VegetableRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->builder = $this->createMock(QueryBuilder::class);
        $classMetadata = new ClassMetadata(Vegetable::class);

        $this->entityManager
            ->method('getClassMetadata')
            ->with(Vegetable::class)
            ->willReturn($classMetadata);

        $this->vegetableRepository = $this->getMockBuilder(VegetableRepository::class)
            ->setConstructorArgs([$this->entityManager])
            ->onlyMethods(['createQueryBuilder'])
            ->getMock();

        $this->vegetableRepository
            ->method('createQueryBuilder')
            ->with('v')
            ->willReturn($this->builder);
    }

    public function testFindById(): void
    {
        $vegetableId = 1;
        $vegetable = new Vegetable($vegetableId, 'Carrot', 100, new \DateTime(), new \DateTime());
        $this->entityManager->expects($this->once())
            ->method('find')
            ->with(Vegetable::class, $vegetableId)
            ->willReturn($vegetable);

        $result = $this->vegetableRepository->findById($vegetableId);

        $this->assertInstanceOf(Vegetable::class, $result);
        $this->assertEquals($vegetableId, $result->getId());
    }

    public function testSave(): void
    {
        $vegetable = new Vegetable(1, 'Carrot', 100, new \DateTime(), new \DateTime());

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($vegetable);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->vegetableRepository->save($vegetable);
    }

    public function testFindByCriteria(): void
    {
        $criteria = new FoodCriteria('1', 'Carrot', 1, 10);

        $this->builder->expects(self::exactly(2))
            ->method('andWhere')
            ->withConsecutive(
                ['v.id = :id'],
                ['v.name LIKE :name']
            )
            ->willReturn($this->builder);

        $this->builder->expects(self::exactly(2))
            ->method('setParameter')
            ->withConsecutive(
                ['id', $criteria->id],
                ['name', '%Carrot%']
            )
            ->willReturn($this->builder);

        $this->builder->expects(self::once())
            ->method('orderBy')
            ->with('v.createdAt', 'DESC')
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
            ->willReturn([new Vegetable(1, 'Carrot', 100, new \DateTime(), new \DateTime())]);

        $this->builder->expects(self::once())
            ->method('getQuery')
            ->willReturn($query);

        $result = $this->vegetableRepository->findByCriteria($criteria);

        $this->assertInstanceOf(VegetablesCollection::class, $result);
        $this->assertCount(1, $result->list());
    }
}
