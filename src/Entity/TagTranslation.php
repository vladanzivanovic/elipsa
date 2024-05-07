<?php

namespace App\Entity;

use App\Repository\TagTranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TagTranslationRepository::class)]
class TagTranslation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'field.not_blank', groups: ['SetTag'])]
    #[Assert\Length(maxMessage: 'field.max_length', groups: ['SetTag'], max: '50')]
    private string $title;

    #[ORM\Column(type: 'string', length: 255)]
    #[Gedmo\Slug(fields: ['title'], updatable: true)]
    private string $slug;

    #[ORM\Column(type: 'string', length: 10)]
    private string $locale;

    #[ORM\ManyToOne(targetEntity: Tags::class, inversedBy: 'tagTranslations')]
    #[ORM\JoinColumn(nullable: false)]
    private Tags $tag;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getTag(): Tags
    {
        return $this->tag;
    }

    public function setTag(Tags $tag): self
    {
        $this->tag = $tag;

        return $this;
    }
}
