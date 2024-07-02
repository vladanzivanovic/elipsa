<?php

namespace App\Entity;

use App\Entity\Resources\CountryResourceInterface;
use App\Entity\Resources\CountryResourceTrait;
use App\Entity\Resources\EntityInterface;
use App\Entity\Resources\ResourceTrait;
use App\Repository\SliderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SliderRepository::class)]
class Slider implements EntityInterface, CountryResourceInterface
{
    use ResourceTrait;
    use CountryResourceTrait;

    public const STATUS_PENDING = false;
    public const STATUS_ACTIVE = true;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive;

    #[ORM\OneToMany(targetEntity: \App\Entity\SliderTranslation::class, mappedBy: 'slider', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $sliderTranslations;

    #[ORM\OneToOne(targetEntity: \App\Entity\Image::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private Image $image;

    #[ORM\Column(type: 'integer')]
    private int $position;

    public function __construct()
    {
        $this->sliderTranslations = new ArrayCollection();
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection<int, SliderTranslation>
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

    public function getByLocale(string $locale): null|SliderTranslation
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
