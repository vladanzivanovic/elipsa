<?php

namespace App\Entity;

use App\Repository\CatalogueTranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=CatalogueTranslationRepository::class)
 */
class CatalogueTranslation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"title"}, updatable=false)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $locale;

    /**
     * @ORM\ManyToOne(targetEntity=Catalogue::class, inversedBy="catalogueTranslations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $catalogue;

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

    public function getCatalogue(): ?Catalogue
    {
        return $this->catalogue;
    }

    public function setCatalogue(?Catalogue $catalogue): self
    {
        $this->catalogue = $catalogue;

        return $this;
    }
}
