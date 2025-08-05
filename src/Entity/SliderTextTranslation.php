<?php

namespace App\Entity;

use App\Entity\Resources\EntityInterface;
use App\Entity\Resources\LocaleInterface;
use App\Entity\Resources\LocaleTrait;
use App\Entity\Resources\ResourceTrait;
use App\Repository\SliderTextTranslationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SliderTextTranslationRepository::class)]
class SliderTextTranslation implements EntityInterface, LocaleInterface
{
    use ResourceTrait;
    use LocaleTrait;

    #[ORM\Column(type: 'string')]
    private string $title;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $link;

    #[ORM\ManyToOne(targetEntity: SliderText::class, inversedBy: 'sliderTextTranslations')]
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

    public function getSliderText(): SliderText
    {
        return $this->sliderText;
    }

    public function setSliderText(SliderText $sliderText): self
    {
        $this->sliderText = $sliderText;

        return $this;
    }

    public function getDescription(): null|string
    {
        return $this->description;
    }

    public function setDescription(null|string $description): void
    {
        $this->description = $description;
    }
}
