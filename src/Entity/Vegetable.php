<?php

namespace App\Entity;

use App\ValueObject\FoodType;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="vegetables")
 */
class Vegetable extends Food
{
    public function __construct(
        string   $id,
        string   $name,
        float    $quantityGrams,
        DateTime $createdAt,
        DateTime $updatedAt
    )
    {
        parent::__construct($id, $name, $quantityGrams, FoodType::Vegetable, $createdAt, $updatedAt);
    }

    public function getType(): FoodType
    {
        return FoodType::Vegetable;
    }
}