<?php

namespace App\Repository;

namespace App\Repository;

use App\Entity\Fruit;
use App\Entity\FruitsCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Exception;

class FruitRepository extends EntityRepository implements FruitRepositoryInterface
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata(Fruit::class));
    }

    public function findById(int $id): ?Fruit
    {
        return $this->entityManager->find(Fruit::class, $id);
    }

    /**  @throws Exception */
    public function save(Fruit $food): void
    {
        $this->entityManager->persist($food);
        $this->entityManager->flush();
    }

    public function findByCriteria(FoodCriteria $criteria): FruitsCollection
    {
        $qb = $this->createQueryBuilder('f');
        $criteria->applyFilters($qb, 'f');
        return new FruitsCollection($qb->getQuery()->getResult());
    }
}