<?php

namespace App\Repository;

use Doctrine\ORM\QueryBuilder;

class FoodCriteria
{
    const DEFAULT_LIMIT = 15;
    const DEFAULT_PAGE = 1;
    public function __construct(
        public ?string $id = null,
        public ?string $name = null,
        public ?int $page = null,
        public ?int $limit = null
    ) {
    }

    public function applyFilters(QueryBuilder $qb, string $alias): void
    {
        if ($this->id) {
            $qb->andWhere($alias . '.id = :id')
                ->setParameter('id', $this->id);
        }

        if ($this->name) {
            $qb->andWhere($alias . '.name LIKE :name')
                ->setParameter('name', '%' . $this->name . '%');
        }

        $qb->orderBy($alias . '.createdAt', 'DESC');

        $qb->setFirstResult((($this->page ?? self::DEFAULT_PAGE) - 1) * ($this->getLimit()))
        ->setMaxResults($this->getLimit());
    }

    private function getLimit(): int
    {
        if (is_null($this->limit) || $this->limit <= 0) {
            return self::DEFAULT_LIMIT;
        }
        return $this->limit;
    }
}