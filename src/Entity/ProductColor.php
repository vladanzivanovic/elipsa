<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductColorRepository")
 */
class ProductColor
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=7)
     * @Assert\NotBlank(message="field.not_blank", groups={"SetColor"})
     */
    private $hex;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ColorTranslation", mappedBy="color", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $colorTranslations;

    public function __construct()
    {
        $this->colorTranslations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection|ColorTranslation[]
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

    /**
     * @param string $locale
     *
     * @return ColorTranslation
     */
    public function getByLocale(string $locale): ColorTranslation
    {
        $filteredTrans = $this->colorTranslations->filter(function ($trans) use ($locale) {
            /** @var ColorTranslation $trans */
            return $trans->getLocale() === $locale;
        });

        return $filteredTrans->first();
    }
}
