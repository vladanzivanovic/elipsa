<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Repository\SliderTextTranslationRepository::class)]
class SliderTextTranslation implements EntityInterface
{
    use ResourceTrait;

    #[ORM\Column(type: 'string')]
    private string $title;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $link;

    #[ORM\Column(type: 'string', length: 2)]
    private string $locale;

    #[ORM\ManyToOne(targetEntity: \App\Entity\SliderText::class, inversedBy: 'sliderTextTranslations')]
    #[ORM\JoinColumn(nullable: false)]
    private SliderText $sliderText;


    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getSliderText(): SliderText
    {
        return $this->sliderText;
    }

    public function setSliderText(?SliderText $sliderText): self
    {
        $this->sliderText = $sliderText;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
