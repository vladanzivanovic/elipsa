<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use SiteBundle\Entity\BlogTranslation;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category implements EntityInterface
{
    use ResourceTrait;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="children")
     */
    private null|Category $parent;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CategoryTranslation", mappedBy="category", orphanRemoval=true, cascade={"persist", "remove"})
     *
     * @var Collection<int, CategoryTranslation>
     */
    private Collection $categoryTranslations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Category", mappedBy="parent")
     *
     * @var Collection<int, Category>
     */
    private Collection $children;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductHasCategories", mappedBy="category")
     *
     * @var Collection<int, ProductHasCategories>
     */
    private Collection $productHasCategories;

    public function __construct()
    {
        $this->categoryTranslations = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->productHasCategories = new ArrayCollection();
    }

    public function getParent(): null | self
    {
        return $this->parent;
    }

    public function setParent(null|self $category): self
    {
        $this->parent = $category;

        return $this;
    }

    /**
     * @return Collection<int, CategoryTranslation>
     */
    public function getCategoryTranslations(): Collection
    {
        return $this->categoryTranslations;
    }

    public function addCategoryTranslation(CategoryTranslation $categoryTranslation): self
    {
        if (!$this->categoryTranslations->contains($categoryTranslation)) {
            $this->categoryTranslations[] = $categoryTranslation;
            $categoryTranslation->setCategory($this);
        }

        return $this;
    }

    public function removeCategoryTranslation(CategoryTranslation $categoryTranslation): self
    {
        if ($this->categoryTranslations->contains($categoryTranslation)) {
            $this->categoryTranslations->removeElement($categoryTranslation);
            // set the owning side to null (unless already changed)
            if ($categoryTranslation->getCategory() === $this) {
                $categoryTranslation->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProductHasCategories>
     */
    public function getProductHasCategories(): Collection
    {
        return $this->productHasCategories;
    }

    public function addProductHasCategory(ProductHasCategories $productHasCategory): self
    {
        if (!$this->productHasCategories->contains($productHasCategory)) {
            $this->productHasCategories[] = $productHasCategory;
            $productHasCategory->setCategory($this);
        }

        return $this;
    }

    public function removeProductHasCategory(ProductHasCategories $productHasCategory): self
    {
        if ($this->productHasCategories->contains($productHasCategory)) {
            $this->productHasCategories->removeElement($productHasCategory);
            // set the owning side to null (unless already changed)
            if ($productHasCategory->getCategory() === $this) {
                $productHasCategory->setCategory(null);
            }
        }

        return $this;
    }

    public function getByLocale(string $locale): null|CategoryTranslation
    {
        $filteredTrans = $this->categoryTranslations->filter(function ($trans) use ($locale) {
            /** @var CategoryTranslation $trans */
            return $trans->getLocale() === $locale;
        });

        return 0 < $filteredTrans->count() ? $filteredTrans->first() : null;
    }
}
