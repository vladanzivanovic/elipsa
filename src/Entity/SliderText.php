<?php

namespace App\Entity;

use App\Entity\Resources\CountryResourceInterface;
use App\Entity\Resources\CountryResourceTrait;
use App\Entity\Resources\EntityInterface;
use App\Entity\Resources\LocaleTranslatorInterface;
use App\Entity\Resources\LocaleTranslatorTrait;
use App\Entity\Resources\ResourceTrait;
use App\Entity\Resources\StatusInterface;
use App\Entity\Resources\StatusTrait;
use App\Repository\SliderTextRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SliderTextRepository::class)]
class SliderText implements EntityInterface, CountryResourceInterface, LocaleTranslatorInterface, StatusInterface
{
    use ResourceTrait;
    use CountryResourceTrait;
    use LocaleTranslatorTrait;
    use StatusTrait;

    public const POSITION_HEADER = 'header';
    public const POSITION_FOOTER = 'footer';

    #[ORM\OneToMany(mappedBy: 'sliderText', targetEntity: SliderTextTranslation::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $translations;

    #[ORM\Column(type: 'string', length: 20, nullable: false)]
    private ?string $position;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    /**
     * @return Collection<int, SliderTranslation>
     */
    public function getSliderTextTranslations(): Collection
    {
        return $this->translations;
    }

    public function addSliderTextTranslation(SliderTextTranslation $sliderTextTranslation): self
    {
        if (!$this->translations->contains($sliderTextTranslation)) {
            $this->translations[] = $sliderTextTranslation;
            $sliderTextTranslation->setSliderText($this);
        }

        return $this;
    }

    public function removeSliderTextTranslation(SliderTextTranslation $sliderTextTranslation): self
    {
        if ($this->translations->contains($sliderTextTranslation)) {
            $this->translations->removeElement($sliderTextTranslation);
            // set the owning side to null (unless already changed)
            if ($sliderTextTranslation->getSliderText() === $this) {
                $sliderTextTranslation->setSliderText(null);
            }
        }

        return $this;
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
