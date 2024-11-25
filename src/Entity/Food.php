<?php

namespace App\Entity;

use App\ValueObject\FoodType;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class Food
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    protected string $id;
    /**
     * @ORM\Column(type="string")
     */
    protected string $name;
    /**
     * @ORM\Column(type="float")
     */
    protected float $quantityGrams;
    protected FoodType $type;
    /**
     * @ORM\Column(type="datetime")
     */
    protected DateTime $createdAt;
    /**
     * @ORM\Column(type="datetime")
     */
    protected DateTime $updatedAt;

    public function __construct(
        string   $id,
        string   $name,
        float    $quantityGrams,
        FoodType $type,
        DateTime $createdAt,
        DateTime $updatedAt
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->quantityGrams = $quantityGrams;
        $this->type = $type;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getQuantityGrams(): float
    {
        return $this->quantityGrams;
    }

    public function getType(): FoodType
    {
        return $this->type;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function toArray(?string $unit = 'g'): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'quantity' => $this->quantityGrams,
            'unit' => 'g',
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s')
        ];
    }
}