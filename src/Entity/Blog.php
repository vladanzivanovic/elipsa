<?php

namespace App\Entity;

use App\Entity\Resources\CountryResourceInterface;
use App\Entity\Resources\CountryResourceTrait;
use App\Entity\Resources\EntityInterface;
use App\Entity\Resources\ResourceTrait;
use App\Repository\BlogRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: BlogRepository::class)]
class Blog implements EntityInterface, CountryResourceInterface
{
    use ResourceTrait;
    use CountryResourceTrait;

    const STATUS_ACTIVE = 1;
    const STATUS_PENDING = 2;

    #[ORM\OneToMany(mappedBy: 'blog', targetEntity: BlogTranslation::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $blogTranslations;

    #[ORM\Column(type: 'smallint')]
    private int $status;

    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'create')]
    private \DateTimeInterface $createdAt;

    #[ORM\OneToMany(mappedBy: 'blog', targetEntity: BlogHasTags::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $blogHasTags;

    #[ORM\OneToOne(targetEntity: Image::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private Image $image;

    public function __construct()
    {
        $this->blogTranslations = new ArrayCollection();
        $this->blogHasTags = new ArrayCollection();
    }

    /**
     * @return Collection<int, BlogTranslation>
     */
    public function getBlogTranslations(): Collection
    {
        return $this->blogTranslations;
    }

    /**
     * @param BlogTranslation $blogTranslation
     *
     * @return $this
     */
    public function addBlogTranslation(BlogTranslation $blogTranslation): self
    {
        if (!$this->blogTranslations->contains($blogTranslation)) {
            $this->blogTranslations[] = $blogTranslation;
            $blogTranslation->setBlog($this);
        }

        return $this;
    }

    public function getBlogTranslationByLocale(string $locale): null|BlogTranslation
    {
        $filteredTrans = $this->blogTranslations->filter(function ($blogTrans) use ($locale) {
            /** @var BlogTranslation $blogTrans */
            return $blogTrans->getLocale() == $locale;
        });

        return false !== $filteredTrans->first() ? $filteredTrans->first() : null;
    }

    /**
     * @param BlogTranslation $blogTranslation
     *
     * @return $this
     */
    public function removeBlogTranslation(BlogTranslation $blogTranslation): self
    {
        if ($this->blogTranslations->contains($blogTranslation)) {
            $this->blogTranslations->removeElement($blogTranslation);
            // set the owning side to null (unless already changed)
            if ($blogTranslation->getBlog() === $this) {
                $blogTranslation->setBlog(null);
            }
        }

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, BlogHasTags>
     */
    public function getBlogHasTags(): Collection
    {
        return $this->blogHasTags;
    }

    public function addBlogHasTag(BlogHasTags $blogHasTag): self
    {
        if (!$this->blogHasTags->contains($blogHasTag)) {
            $this->blogHasTags[] = $blogHasTag;
            $blogHasTag->setBlog($this);
        }

        return $this;
    }

    public function removeBlogHasTag(BlogHasTags $blogHasTag): self
    {
        if ($this->blogHasTags->contains($blogHasTag)) {
            $this->blogHasTags->removeElement($blogHasTag);
            // set the owning side to null (unless already changed)
            if ($blogHasTag->getBlog() === $this) {
                $blogHasTag->setBlog(null);
            }
        }

        return $this;
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    public function setImage(Image $image): self
    {
        $this->image = $image;

        return $this;
    }
}
