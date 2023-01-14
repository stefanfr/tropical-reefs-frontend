<?php

namespace App\Entity\Manager;

use App\Repository\Manager\ProductModelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductModelRepository::class)]
#[ORM\Table(name: 'manager_product_model')]
class ProductModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sku = null;

    #[ORM\Column(length: 255)]
    private ?string $brand = null;

    #[ORM\Column]
    private array $productData = [];

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Product::class)]
    private Collection $childProducts;

    public function __construct()
    {
        $this->childProducts = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Product>
     */
    public function getChildProducts(): Collection
    {
        return $this->childProducts;
    }

    public function addChildProduct(Product $childProduct): self
    {
        if ( ! $this->childProducts->contains($childProduct)) {
            $this->childProducts->add($childProduct);
            $childProduct->setParent($this);
        }

        return $this;
    }

    public function removeChildProduct(Product $childProduct): self
    {
        if ($this->childProducts->removeElement($childProduct)) {
            // set the owning side to null (unless already changed)
            if ($childProduct->getParent() === $this) {
                $childProduct->setParent(null);
            }
        }

        return $this;
    }
}
