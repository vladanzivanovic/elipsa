<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Repository\TagsRepository::class)]
class Tags
{
    public const TYPE_PRODUCT = 1;
    public const TYPE_BLOG = 2;

    public const PRODUCT_TYPE_SEASON = 'season';

    public const PRODUCT_TYPE_COLLECTION = 'collection';

    public const PRODUCT_TYPE_ATTRIBUTE = 'attribute';

    public const PRODUCT_TYPE_PROMOTION = 'promotion';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $relatedType;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $productType = null;

    #[ORM\OneToMany(mappedBy: 'tag', targetEntity: TagTranslation::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $tagTranslations;

    #[ORM\OneToMany(mappedBy: 'tag', targetEntity: ProductHasTags::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $productHasTags;

    public function __construct()
    {
        $this->tagTranslations = new ArrayCollection();
        $this->productHasTags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRelatedType(): ?int
    {
        return $this->relatedType;
    }

    public function setRelatedType(int $relatedType): self
    {
        $this->relatedType = $relatedType;

        return $this;
    }

    public function getProductType(): ?string
    {
        return $this->productType;
    }

    public function setProductType(string $productType): void
    {
        $this->productType = $productType;
    }

    /**
     * @return Collection<int, TagTranslation>
     */
    public function getTagTranslations(): Collection
    {
        return $this->tagTranslations;
    }

    public function addTagTranslation(TagTranslation $tagTranslation): self
    {
        if (!$this->tagTranslations->contains($tagTranslation)) {
            $this->tagTranslations[] = $tagTranslation;
            $tagTranslation->setTag($this);
        }

        return $this;
    }

    public function removeTagTranslation(TagTranslation $tagTranslation): self
    {
        if ($this->tagTranslations->removeElement($tagTranslation)) {
            // set the owning side to null (unless already changed)
            if ($tagTranslation->getTag() === $this) {
                $tagTranslation->setTag(null);
            }
        }

        return $this;
    }

    public function getByLocale(string $locale): null|TagTranslation
    {
        $filteredTrans = $this->tagTranslations->filter(function ($trans) use ($locale) {
            /** @var TagTranslation $trans */
            return $trans->getLocale() === $locale;
        });

        return false !== $filteredTrans->first() ? $filteredTrans->first() : null;
    }

    public function getProductHasTags(): Collection
    {
        return $this->productHasTags;
    }
}
