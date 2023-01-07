<?php

namespace App\Entity\Manager\Product;

use App\Repository\Manager\Product\ImagesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImagesRepository::class)]
#[ORM\Table(name: 'manager_product_images')]
class Images
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?Draft $product = null;

    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Draft
    {
        return $this->product;
    }

    public function setProduct(?Draft $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }
}
