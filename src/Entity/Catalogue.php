<?php

namespace App\Entity;

use App\Repository\CatalogueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CatalogueRepository::class)
 */
class Catalogue
{
    public const STATUS_PENDING = 1;
    public const STATUS_ACTIVE = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=CatalogueTranslation::class, mappedBy="catalogue", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $catalogueTranslations;

    /**
     * @ORM\OneToMany(targetEntity=CatalogueHasImages::class, mappedBy="catalogue", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $catalogueHasImages;

    public function __construct()
    {
        $this->catalogueTranslations = new ArrayCollection();
        $this->catalogueHasImages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|CatalogueTranslation[]
     */
    public function getCatalogueTranslations(): Collection
    {
        return $this->catalogueTranslations;
    }

    public function addCatalogueTranslation(CatalogueTranslation $catalogueTranslation): self
    {
        if (!$this->catalogueTranslations->contains($catalogueTranslation)) {
            $this->catalogueTranslations[] = $catalogueTranslation;
            $catalogueTranslation->setCatalogue($this);
        }

        return $this;
    }

    public function removeCatalogueTranslation(CatalogueTranslation $catalogueTranslation): self
    {
        if ($this->catalogueTranslations->contains($catalogueTranslation)) {
            $this->catalogueTranslations->removeElement($catalogueTranslation);
            // set the owning side to null (unless already changed)
            if ($catalogueTranslation->getCatalogue() === $this) {
                $catalogueTranslation->setCatalogue(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CatalogueHasImages[]
     */
    public function getCatalogueHasImages(): Collection
    {
        return $this->catalogueHasImages;
    }

    public function addCatalogueHasImage(CatalogueHasImages $catalogueHasImage): self
    {
        if (!$this->catalogueHasImages->contains($catalogueHasImage)) {
            $this->catalogueHasImages[] = $catalogueHasImage;
            $catalogueHasImage->setCatalogue($this);
        }

        return $this;
    }

    public function removeCatalogueHasImage(CatalogueHasImages $catalogueHasImage): self
    {
        if ($this->catalogueHasImages->contains($catalogueHasImage)) {
            $this->catalogueHasImages->removeElement($catalogueHasImage);
            // set the owning side to null (unless already changed)
            if ($catalogueHasImage->getCatalogue() === $this) {
                $catalogueHasImage->setCatalogue(null);
            }
        }

        return $this;
    }

    /**
     * @param string $locale
     *
     * @return CatalogueTranslation
     */
    public function getByLocale(string $locale): CatalogueTranslation
    {
        $trans = $this->getCatalogueTranslations();

        $filteredTrans = $trans->filter(function ($translation) use ($locale) {
            /** @var CatalogueTranslation $translation */
            return $translation->getLocale() === $locale;
        });

        return $filteredTrans->first();
    }
}
