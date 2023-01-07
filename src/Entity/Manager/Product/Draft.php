<?php

namespace App\Entity\Manager\Product;

use App\Repository\Manager\Product\DraftRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DraftRepository::class)]
#[ORM\Table(name: 'manager_product_draft')]
#[ORM\Index(columns: ['sku'], name: 'index_sku')]
class Draft
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(unique: true)]
    protected string $sku;

    #[ORM\Column]
    protected string $category;

    #[ORM\Column(type: 'text', nullable: false)]
    protected string $name;

    #[ORM\Column]
    protected float $rrp;

    #[ORM\Column]
    protected float $npp;

    #[ORM\Column(nullable: true)]
    protected ?string $ean = null;

    #[ORM\Column(nullable: true)]
    protected ?string $tariff = null;

    #[ORM\Column(nullable: true)]
    protected ?float $weight = null;

    #[ORM\Column(nullable: true)]
    protected ?string $size = null;

    #[ORM\Column]
    protected int $tax_class;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Images::class)]
    private Collection $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     * @return Draft
     */
    public function setSku(string $sku): Draft
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @param string $category
     * @return Draft
     */
    public function setCategory(string $category): Draft
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Draft
     */
    public function setName(string $name): Draft
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return float
     */
    public function getRrp(): float
    {
        return $this->rrp;
    }

    /**
     * @param float $rrp
     * @return Draft
     */
    public function setRrp(float $rrp): Draft
    {
        $this->rrp = $rrp;
        return $this;
    }

    /**
     * @return float
     */
    public function getNpp(): float
    {
        return $this->npp;
    }

    /**
     * @param float $npp
     * @return Draft
     */
    public function setNpp(float $npp): Draft
    {
        $this->npp = $npp;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEan(): ?string
    {
        return $this->ean;
    }

    /**
     * @param string|null $ean
     * @return Draft
     */
    public function setEan(?string $ean): Draft
    {
        $this->ean = $ean;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTariff(): ?string
    {
        return $this->tariff;
    }

    /**
     * @param string|null $tariff
     * @return Draft
     */
    public function setTariff(?string $tariff): Draft
    {
        $this->tariff = $tariff;
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
     * @return Draft
     */
    public function setWeight(?float $weight): Draft
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * @return ?string
     */
    public function getSize(): ?string
    {
        return $this->size;
    }

    /**
     * @param ?string $size
     * @return Draft
     */
    public function setSize(?string $size): Draft
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return int
     */
    public function getTaxClass(): int
    {
        return $this->tax_class;
    }

    /**
     * @param int $tax_class
     * @return Draft
     */
    public function setTaxClass(int $tax_class): Draft
    {
        $this->tax_class = $tax_class;
        return $this;
    }

    /**
     * @return Collection<int, Images>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(Images $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

        return $this;
    }
}
