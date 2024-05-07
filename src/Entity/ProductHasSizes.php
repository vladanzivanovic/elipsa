<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Repository\ProductHasSizesRepository::class)]
class ProductHasSizes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: \App\Entity\ProductSize::class, inversedBy: 'productHasSizes')]
    private ProductSize $size;

    #[ORM\ManyToOne(targetEntity: \App\Entity\Product::class, inversedBy: 'productHasSizes')]
    #[ORM\JoinColumn(nullable: false)]
    private Product $product;

    #[ORM\Column(type: 'integer')]
    private int $quantity = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSize(): ?ProductSize
    {
        return $this->size;
    }

    public function setSize(?ProductSize $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }
}
