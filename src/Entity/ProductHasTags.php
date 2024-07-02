<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Repository\ProductHasTagsRepository::class)]
class ProductHasTags
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: \App\Entity\Product::class, inversedBy: 'productHasTags')]
    #[ORM\JoinColumn(nullable: false)]
    private Product $product;

    #[ORM\ManyToOne(targetEntity: \App\Entity\Tags::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Tags $tag;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getTag(): Tags
    {
        return $this->tag;
    }

    public function setTag(Tags $tag): self
    {
        $this->tag = $tag;

        return $this;
    }
}
