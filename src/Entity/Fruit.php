<?php

namespace App\Entity;

use App\ValueObject\FoodType;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fruits")
 */
class Fruit extends Food
{
    public function __construct(
        string   $id,
        string   $name,
        float    $quantityGrams,
        DateTime $createdAt,
        DateTime $updatedAt
    )
    {
        parent::__construct($id, $name, $quantityGrams, FoodType::Fruit, $createdAt, $updatedAt);
    }

    public function getType(): FoodType
    {
        return FoodType::Fruit;
    }
}