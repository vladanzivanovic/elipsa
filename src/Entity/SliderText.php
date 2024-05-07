<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Repository\SliderTextRepository::class)]
class SliderText implements EntityInterface
{
    use ResourceTrait;

    public const STATUS_PENDING = false;
    public const STATUS_ACTIVE = true;

    public const POSITION_HEADER = 'header';
    public const POSITION_FOOTER = 'footer';

    #[ORM\Column(type: 'boolean')]
    private ?bool $isActive = null;

    #[ORM\OneToMany(targetEntity: \App\Entity\SliderTextTranslation::class, mappedBy: 'sliderText', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $sliderTextTranslations;

    #[ORM\Column(type: 'string', length: 20, nullable: false)]
    private ?string $position;

    public function __construct()
    {
        $this->sliderTextTranslations = new ArrayCollection();
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
     * @return Collection<int, SliderTranslation>
     */
    public function getSliderTextTranslations(): Collection
    {
        return $this->sliderTextTranslations;
    }

    public function addSliderTextTranslation(SliderTextTranslation $sliderTextTranslation): self
    {
        if (!$this->sliderTextTranslations->contains($sliderTextTranslation)) {
            $this->sliderTextTranslations[] = $sliderTextTranslation;
            $sliderTextTranslation->setSliderText($this);
        }

        return $this;
    }

    public function removeSliderTextTranslation(SliderTextTranslation $sliderTextTranslation): self
    {
        if ($this->sliderTextTranslations->contains($sliderTextTranslation)) {
            $this->sliderTextTranslations->removeElement($sliderTextTranslation);
            // set the owning side to null (unless already changed)
            if ($sliderTextTranslation->getSliderText() === $this) {
                $sliderTextTranslation->setSliderText(null);
            }
        }

        return $this;
    }

    public function getByLocale(string $locale): SliderTextTranslation|null
    {
        $transCollection = $this->sliderTextTranslations;

        $filtered = $transCollection->filter(function ($trans) use ($locale) {
            /** @var SliderTextTranslation $trans */
            return $trans->getLocale() === $locale;
        });

        return 0 < $filtered->count() ? $filtered->first() : null;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): void
    {
        $this->position = $position;
    }
}
