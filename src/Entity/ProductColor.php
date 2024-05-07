<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: \App\Repository\ProductColorRepository::class)]
class ProductColor implements EntityInterface
{
    use ResourceTrait;

    #[ORM\Column(type: 'string', length: 7)]
    #[Assert\NotBlank(message: 'field.not_blank', groups: ['SetColor'])]
    private string $hex;

    /**
     * @var Collection<int, ColorTranslation>
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\ColorTranslation::class, mappedBy: 'color', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $colorTranslations;

    /**
     * @var Collection<int, OrderProduct>
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\OrderProduct::class, mappedBy: 'color')]
    private Collection $orderProducts;

    public function __construct()
    {
        $this->colorTranslations = new ArrayCollection();
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
        return $this->colorTranslations;
    }

    public function addColorTranslation(ColorTranslation $colorTranslation): self
    {
        if (!$this->colorTranslations->contains($colorTranslation)) {
            $this->colorTranslations[] = $colorTranslation;
            $colorTranslation->setColor($this);
        }

        return $this;
    }

    public function removeColorTranslation(ColorTranslation $colorTranslation): self
    {
        if ($this->colorTranslations->contains($colorTranslation)) {
            $this->colorTranslations->removeElement($colorTranslation);
            // set the owning side to null (unless already changed)
            if ($colorTranslation->getColor() === $this) {
                $colorTranslation->setColor(null);
            }
        }

        return $this;
    }

    public function getByLocale(string $locale): ColorTranslation|null
    {
        $filteredTrans = $this->colorTranslations->filter(function ($trans) use ($locale) {
            /** @var ColorTranslation $trans */
            return $trans->getLocale() === $locale;
        });

        return 0 < $filteredTrans->count() ? $filteredTrans->first() : null;
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
