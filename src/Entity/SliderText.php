<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SliderTextRepository")
 */
class SliderText
{
    public const STATUS_PENDING = false;
    public const STATUS_ACTIVE = true;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SliderTextTranslation", mappedBy="sliderText", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $sliderTextTranslations;

    public function __construct()
    {
        $this->sliderTextTranslations = new ArrayCollection();
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

    /**
     * @param string $locale
     *
     * @return SliderTextTranslation
     */
    public function getByLocale(string $locale): SliderTextTranslation
    {
        $transCollection = $this->sliderTextTranslations;

        $filtered = $transCollection->filter(function ($trans) use ($locale) {
            /** @var SliderTextTranslation $trans */
            return $trans->getLocale() === $locale;
        });

        return $filtered->first();
    }
}
