<?php

namespace App\Entity;

use App\Entity\Resources\EntityInterface;
use App\Entity\Resources\LocaleInterface;
use App\Entity\Resources\LocaleTrait;
use App\Entity\Resources\ResourceTrait;
use App\Repository\CareerDescriptionTranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: CareerDescriptionTranslationRepository::class)]
class CareerDescriptionTranslation implements EntityInterface, LocaleInterface
{
    use ResourceTrait;
    use LocaleTrait;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\ManyToOne(targetEntity: CareerDescription::class, inversedBy: 'careerDescriptionTranslations')]
    #[ORM\JoinColumn(nullable: false)]
    private CareerDescription $careerDescription;

    #[ORM\Column(type: 'string', length: 255)]
    #[Gedmo\Slug(fields: ['title'], updatable: true)]
    private string $slug;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCareerDescription(): ?CareerDescription
    {
        return $this->careerDescription;
    }

    public function setCareerDescription(?CareerDescription $careerDescription): self
    {
        $this->careerDescription = $careerDescription;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
