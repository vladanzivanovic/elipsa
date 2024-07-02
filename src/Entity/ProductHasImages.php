<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Repository\ProductHasImagesRepository::class)]
class ProductHasImages
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: \App\Entity\Product::class, inversedBy: 'productHasImages')]
    #[ORM\JoinColumn(nullable: false)]
    private $product;

    #[ORM\OneToOne(targetEntity: \App\Entity\Image::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private $image;

    #[ORM\ManyToOne(targetEntity: \App\Entity\ProductColor::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $color;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getColor(): ?ProductColor
    {
        return $this->color;
    }

    public function setColor(?ProductColor $color): self
    {
        $this->color = $color;

        return $this;
    }
}
