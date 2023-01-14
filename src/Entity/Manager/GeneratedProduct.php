<?php

namespace App\Entity\Manager;

use App\Repository\Manager\GeneratedProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GeneratedProductRepository::class)]
#[ORM\Table(name: 'manager_generated_product')]
class GeneratedProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $sku = null;

    #[ORM\Column(type: 'text')]
    protected ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $parent = null;

    #[ORM\Column]
    protected ?bool $enabled = true;

    #[ORM\Column(length: 255)]
    protected ?string $family = null;

    #[ORM\Column(length: 255)]
    protected ?string $categories = null;

    #[ORM\Column(length: 255)]
    protected ?string $brand = null;

    #[ORM\Column]
    protected ?bool $productEnabled = true;

    #[ORM\Column(length: 255)]
    protected ?string $supplierCode = null;

    #[ORM\Column(precision: 4, scale: 10, nullable: true)]
    protected ?float $weight = null;

    #[ORM\Column(precision: 4, scale: 10)]
    protected ?float $salesPrice = null;

    #[ORM\Column(precision: 4, scale: 10)]
    protected ?float $purchasePrice = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $eanCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $tax = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $productCode = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(?string $sku): self
    {
        $this->sku = $sku;

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

    public function getParent(): ?string
    {
        return $this->parent;
    }

    public function setParent(?string $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getFamily(): ?string
    {
        return $this->family;
    }

    public function setFamily(string $family): self
    {
        $this->family = $family;

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

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

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

    public function getSupplierCode(): ?string
    {
        return $this->supplierCode;
    }

    public function setSupplierCode(string $supplierCode): self
    {
        $this->supplierCode = $supplierCode;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getWeight(): ?float
    {
        return $this->weight;
    }

    /**
     * @param float|null $weight
     * @return GeneratedProduct
     */
    public function setWeight(?float $weight): GeneratedProduct
    {
        $this->weight = $weight;
        return $this;
    }

    public function getSalesPrice(): ?float
    {
        return $this->salesPrice;
    }

    public function setSalesPrice(float $salesPrice): self
    {
        $this->salesPrice = $salesPrice;

        return $this;
    }

    public function getPurchasePrice(): ?float
    {
        return $this->purchasePrice;
    }

    public function setPurchasePrice(float $purchasePrice): self
    {
        $this->purchasePrice = $purchasePrice;

        return $this;
    }

    public function getEanCode(): ?string
    {
        return $this->eanCode;
    }

    public function setEanCode(?string $eanCode): self
    {
        $this->eanCode = $eanCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTax(): ?string
    {
        return $this->tax;
    }

    /**
     * @param string|null $tax
     * @return GeneratedProduct
     */
    public function setTax(?string $tax): GeneratedProduct
    {
        $this->tax = $tax;
        return $this;
    }

    public function getProductCode(): ?string
    {
        return $this->productCode;
    }

    public function setProductCode(?string $productCode): self
    {
        $this->productCode = $productCode;

        return $this;
    }
}
