<?php

namespace App\Entity;

use App\Entity\Resources\CountryResourceInterface;
use App\Entity\Resources\CountryResourceTrait;
use App\Entity\Resources\EntityInterface;
use App\Entity\Resources\ResourceTrait;
use App\Repository\CatalogueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CatalogueRepository::class)]
class Catalogue implements EntityInterface, CountryResourceInterface
{
    use ResourceTrait;
    use CountryResourceTrait;

    public const STATUS_PENDING = 1;
    public const STATUS_ACTIVE = 2;

    #[ORM\Column(type: 'smallint')]
    private int $status;

    #[ORM\OneToMany(mappedBy: 'catalogue', targetEntity: CatalogueTranslation::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $catalogueTranslations;

    #[ORM\OneToMany(mappedBy: 'catalogue', targetEntity: CatalogueHasImages::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $catalogueHasImages;

    public function __construct()
    {
        $this->catalogueTranslations = new ArrayCollection();
        $this->catalogueHasImages = new ArrayCollection();
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
     * @return Collection<int, CatalogueTranslation>
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
     * @return Collection<int, CatalogueHasImages>
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

    public function getByLocale(string $locale): null|CatalogueTranslation
    {
        $trans = $this->getCatalogueTranslations();

        $filteredTrans = $trans->filter(function ($translation) use ($locale) {
            /** @var CatalogueTranslation $translation */
            return $translation->getLocale() === $locale;
        });

        return false !== $filteredTrans->first() ? $filteredTrans->first() : null;
    }

    public function getMainImage(): null|Image
    {
        foreach ($this->getCatalogueHasImages() as $catalogueHasImage) {
            $image = $catalogueHasImage->getImage();

            if (true === $image->getIsMain()) {
                return $image;
            }
        }

        return null;
    }
}
