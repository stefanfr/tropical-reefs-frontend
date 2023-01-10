<?php

namespace App\Entity\Manager;

use App\Repository\Manager\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: 'manager_product')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $sku = null;

    #[ORM\Column(length: 255)]
    private ?string $supplierCode = null;

    #[ORM\Column(length: 255)]
    private ?string $brand = null;

    #[ORM\Column]
    private array $productData = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(string $sku): self
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSupplierCode(): ?string
    {
        return $this->supplierCode;
    }

    /**
     * @param string|null $supplierCode
     * @return Product
     */
    public function setSupplierCode(?string $supplierCode): Product
    {
        $this->supplierCode = $supplierCode;
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

    public function getProductData(): array
    {
        return $this->productData;
    }

    public function setProductData(array $productData): self
    {
        $this->productData = $productData;

        return $this;
    }
}
