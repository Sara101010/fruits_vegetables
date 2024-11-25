<?php

namespace App\Tests\App\Repository;

use App\Repository\FoodCriteria;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

class FoodCriteriaTest extends TestCase
{
    public function testApplyFilters(): void
    {
        $criteria = new FoodCriteria( '123', 'Apple', 2, 10);
        $qb = $this->createMock(QueryBuilder::class);

        $qb->expects($this->exactly(2))
            ->method('andWhere')
            ->withConsecutive(
                ['alias.id = :id'],
                ['alias.name LIKE :name']
            )
            ->willReturnSelf();;

        $qb->expects($this->exactly(2))
            ->method('setParameter')
            ->withConsecutive(
                ['id', '123'],
                ['name', '%Apple%']
            )
            ->willReturnSelf();

        $qb->expects($this->once())
            ->method('setFirstResult')
            ->with(10)
            ->willReturnSelf();

        $qb->expects($this->once())
            ->method('setMaxResults')
            ->with(10)
            ->willReturnSelf();

        $criteria->applyFilters($qb, 'alias');
    }

    public function testApplyFiltersWithDefaultValues(): void
    {
        $criteria = new FoodCriteria();
        $qb = $this->createMock(QueryBuilder::class);

        $qb->expects($this->once())
            ->method('setFirstResult')
            ->with(0)
            ->willReturnSelf();

        $qb->expects($this->once())
            ->method('setMaxResults')
            ->with(15)
            ->willReturnSelf();

        $criteria->applyFilters($qb, 'alias');
    }

    public function testApplyFiltersWithInvalidLimit(): void
    {
        $criteria = new FoodCriteria(limit: -5);
        $qb = $this->createMock(QueryBuilder::class);

        $qb->expects($this->once())
            ->method('setFirstResult')
            ->with(0)
            ->willReturnSelf();

        $qb->expects($this->once())
            ->method('setMaxResults')
            ->with(15)
            ->willReturnSelf();

        $criteria->applyFilters($qb, 'alias');
    }
}