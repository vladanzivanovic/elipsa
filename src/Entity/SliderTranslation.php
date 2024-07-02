<?php

namespace App\Entity;

use App\Repository\SliderTranslationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SliderTranslationRepository::class)]
class SliderTranslation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'text', nullable: true)]
    private null|string $description = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $buttonLink;

    #[ORM\Column(type: 'string', length: 2)]
    private ?string $locale;

    #[ORM\ManyToOne(targetEntity: Slider::class, inversedBy: 'sliderTranslations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Slider $slider;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): null|string
    {
        return $this->description;
    }

    public function setDescription(null|string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getButtonLink(): ?string
    {
        return $this->buttonLink;
    }

    public function setButtonLink(string $buttonLink): self
    {
        $this->buttonLink = $buttonLink;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getSlider(): ?Slider
    {
        return $this->slider;
    }

    public function setSlider(?Slider $slider): self
    {
        $this->slider = $slider;

        return $this;
    }
}
