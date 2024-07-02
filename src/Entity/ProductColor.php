<?php

namespace App\Entity;

use App\Entity\Resources\EntityInterface;
use App\Entity\Resources\LocaleTranslatorInterface;
use App\Entity\Resources\LocaleTranslatorTrait;
use App\Entity\Resources\ResourceTrait;
use App\Repository\ProductColorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductColorRepository::class)]
class ProductColor implements EntityInterface, LocaleTranslatorInterface
{
    use ResourceTrait;
    use LocaleTranslatorTrait;

    #[ORM\Column(type: 'string', length: 7)]
    #[Assert\NotBlank(message: 'field.not_blank', groups: ['SetColor'])]
    private string $hex;

    /**
     * @var Collection<int, ColorTranslation>
     */
    #[ORM\OneToMany(targetEntity: ColorTranslation::class, mappedBy: 'color', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $translations;

    /**
     * @var Collection<int, OrderProduct>
     */
    #[ORM\OneToMany(targetEntity: OrderProduct::class, mappedBy: 'color')]
    private Collection $orderProducts;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->orderProducts = new ArrayCollection();
    }

    public function getHex(): ?string
    {
        return $this->hex;
    }

    public function setHex(string $hex): self
    {
        $this->hex = $hex;

        return $this;
    }

    /**
     * @return Collection<int, ColorTranslation>
     */
    public function getColorTranslations(): Collection
    {
        return $this->translations;
    }

    public function addColorTranslation(ColorTranslation $colorTranslation): self
    {
        if (!$this->translations->contains($colorTranslation)) {
            $this->translations[] = $colorTranslation;
            $colorTranslation->setColor($this);
        }

        return $this;
    }

    public function removeColorTranslation(ColorTranslation $colorTranslation): self
    {
        if ($this->translations->contains($colorTranslation)) {
            $this->translations->removeElement($colorTranslation);
        }

        return $this;
    }

    /**
     * @return Collection<int, OrderProduct>
     */
    public function getOrderProducts(): Collection
    {
        return $this->orderProducts;
    }

    public function addOrderProduct(OrderProduct $orderProduct): self
    {
        if (!$this->orderProducts->contains($orderProduct)) {
            $this->orderProducts[] = $orderProduct;
            $orderProduct->setColor($this);
        }

        return $this;
    }

    public function removeOrderProduct(OrderProduct $orderProduct): self
    {
        if ($this->orderProducts->contains($orderProduct)) {
            $this->orderProducts->removeElement($orderProduct);
            // set the owning side to null (unless already changed)
            if ($orderProduct->getColor() === $this) {
                $orderProduct->setColor(null);
            }
        }

        return $this;
    }
}
