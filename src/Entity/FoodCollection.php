<?php

namespace App\Entity;

abstract class FoodCollection
{
    protected array $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function add(object $item): void
    {
        if (!$this->isValidType($item)) {
            throw new \InvalidArgumentException('Invalid item type for this collection.');
        }

        $this->items[] = $item;
    }

    public function remove(object $item): void
    {
        $this->items = array_filter($this->items, fn($current) => $current !== $item);
    }

    public function list(): array
    {
        return $this->items;
    }

    public function search(array $filters): array
    {
        return array_filter($this->items, function (Food $food) use ($filters) {
            return $this->matchesAllFilters($food, $filters);
        });
    }

    private function matchesAllFilters(Food $food, array $filters): bool
    {
        foreach ($filters as $key => $value) {
            if (!$this->matchesFilter($food, $key, $value)) {
                return false;
            }
        }
        return true;
    }

    private function matchesFilter(Food $food, string $key, mixed $value): bool
    {
        if (property_exists($food, $key)) {
            return $food->$key === $value;
        }

        return false;
    }

    public function toArray(): array
    {
        foreach ($this->items as $item) {
            $arrayItems[] = $item->toArray();
        }
        return $arrayItems ?? [];
    }

    abstract protected function isValidType(object $item): bool;
}