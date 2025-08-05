<?php

namespace App\Entity;

use App\Entity\Resources\EntityInterface;
use App\Entity\Resources\LocaleInterface;
use App\Entity\Resources\LocaleTrait;
use App\Entity\Resources\ResourceTrait;
use App\Repository\ProductHasTagsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductHasTagsRepository::class)]
class ProductHasTags implements EntityInterface, LocaleInterface
{
    use ResourceTrait;
    use LocaleTrait;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'productHasTags')]
    #[ORM\JoinColumn(nullable: false)]
    private Product $product;

    #[ORM\ManyToOne(targetEntity: Tags::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Tags $tag;

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
