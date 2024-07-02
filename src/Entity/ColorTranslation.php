<?php

namespace App\Entity;

use App\Entity\Resources\EntityInterface;
use App\Entity\Resources\LocaleInterface;
use App\Entity\Resources\LocaleTrait;
use App\Entity\Resources\ResourceTrait;
use App\Repository\ColorTranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: ColorTranslationRepository::class)]
class ColorTranslation implements EntityInterface, LocaleInterface
{
    use ResourceTrait;
    use LocaleTrait;

    #[ORM\Column(type: 'string', length: 50)]
    private string $title;

    #[ORM\Column(type: 'string', length: 50)]
    #[Gedmo\Slug(fields: ['title'], updatable: true)]
    private string $slug;

    #[ORM\ManyToOne(targetEntity: ProductColor::class, inversedBy: 'colorTranslations')]
    #[ORM\JoinColumn(nullable: false)]
    private ProductColor $color;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): null|string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getColor(): ProductColor
    {
        return $this->color;
    }

    public function setColor(ProductColor $color): self
    {
        $this->color = $color;

        return $this;
    }
}
