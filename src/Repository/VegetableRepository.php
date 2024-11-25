<?php

namespace App\Repository;

namespace App\Repository;

use App\Entity\Vegetable;
use App\Entity\VegetablesCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class VegetableRepository extends EntityRepository implements VegetableRepositoryInterface
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata(Vegetable::class));
    }

    public function findById(int $id): ?Vegetable
    {
        return $this->entityManager->find(Vegetable::class, $id);
    }

    /**  @throws \Exception */
    public function save(Vegetable $food): void
    {
        $this->entityManager->persist($food);
        $this->entityManager->flush();
    }

    public function findByCriteria(FoodCriteria $criteria): VegetablesCollection
    {
        $qb = $this->createQueryBuilder('v');
        $criteria->applyFilters($qb, 'v');
        return new VegetablesCollection($qb->getQuery()->getResult());
    }
}
