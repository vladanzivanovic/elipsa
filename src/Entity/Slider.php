<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Repository\SliderRepository::class)]
class Slider
{
    public const STATUS_PENDING = false;
    public const STATUS_ACTIVE = true;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'boolean')]
    private $isActive;

    #[ORM\OneToMany(targetEntity: \App\Entity\SliderTranslation::class, mappedBy: 'slider', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private $sliderTranslations;

    #[ORM\OneToOne(targetEntity: \App\Entity\Image::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $image;

    #[ORM\Column(type: 'integer')]
    private $position;

    public function __construct()
    {
        $this->sliderTranslations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection|SliderTranslation[]
     */
    public function getSliderTranslations(): Collection
    {
        return $this->sliderTranslations;
    }

    public function addSliderTranslation(SliderTranslation $sliderTranslation): self
    {
        if (!$this->sliderTranslations->contains($sliderTranslation)) {
            $this->sliderTranslations[] = $sliderTranslation;
            $sliderTranslation->setSlider($this);
        }

        return $this;
    }

    public function removeSliderTranslation(SliderTranslation $sliderTranslation): self
    {
        if ($this->sliderTranslations->contains($sliderTranslation)) {
            $this->sliderTranslations->removeElement($sliderTranslation);
            // set the owning side to null (unless already changed)
            if ($sliderTranslation->getSlider() === $this) {
                $sliderTranslation->setSlider(null);
            }
        }

        return $this;
    }

    public function getByLocale(string $locale): SliderTranslation|null
    {
        $transCollection = $this->sliderTranslations;

        $filtered = $transCollection->filter(function ($trans) use ($locale) {
            /** @var SliderTranslation $trans */
            return $trans->getLocale() === $locale;
        });

        return 0 < $filtered->count() ? $filtered->first() : null;
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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }
}
