<?php

namespace App\Entity\Manager;

use App\Repository\Manager\GeneratedProductModelRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GeneratedProductModelRepository::class)]
#[ORM\Table(name: 'manager_generated_product_model')]
class GeneratedProductModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $familyVariant = null;

    #[ORM\Column(length: 255)]
    private ?string $categories = null;

    #[ORM\Column]
    private ?bool $productEnabled = null;

    #[ORM\Column(length: 255)]
    private ?string $brand = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getFamilyVariant(): ?string
    {
        return $this->familyVariant;
    }

    public function setFamilyVariant(string $familyVariant): self
    {
        $this->familyVariant = $familyVariant;

        return $this;
    }

    public function getCategories(): ?string
    {
        return $this->categories;
    }

    public function setCategories(string $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    public function isProductEnabled(): ?bool
    {
        return $this->productEnabled;
    }

    public function setProductEnabled(bool $productEnabled): self
    {
        $this->productEnabled = $productEnabled;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
