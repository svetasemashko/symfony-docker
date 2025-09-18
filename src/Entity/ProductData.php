<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: "tblProductData")]
class ProductData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "intProductDataId", type: "integer", options: ["unsigned" => true])]
    private ?int $id = null;

    #[ORM\Column(name: "strProductName", length: 50)]
    private string $name;

    #[ORM\Column(name: "strProductDesc", length: 255)]
    private string $description;

    #[ORM\Column(name: "strProductCode", length: 10, unique: true)]
    private string $code;

    #[ORM\Column(name: "dtmAdded", type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $added = null;

    #[ORM\Column(name: "dtmDiscontinued", type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $discontinued = null;

    #[ORM\Column(name: "stmTimestamp", type: Types::DATETIME_MUTABLE, options: ["default" => "CURRENT_TIMESTAMP"])]
    private DateTimeInterface $timestamp;

    #[ORM\Column(name: "decPrice", type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $price = '0.00';

    #[ORM\Column(name: "intStockLevel", type: Types::INTEGER)]
    private int $stockLevel = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getAdded(): ?DateTimeInterface
    {
        return $this->added;
    }

    public function setAdded(?DateTimeInterface $added): self
    {
        $this->added = $added;
        return $this;
    }

    public function getDiscontinued(): ?DateTimeInterface
    {
        return $this->discontinued;
    }

    public function setDiscontinued(?DateTimeInterface $discontinued): self
    {
        $this->discontinued = $discontinued;

        return $this;
    }

    public function getTimestamp(): DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getStockLevel(): int
    {
        return $this->stockLevel;
    }

    public function setStockLevel(int $stockLevel): self
    {
        $this->stockLevel = $stockLevel;

        return $this;
    }
}
