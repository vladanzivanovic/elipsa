<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BlogRepository")
 */
class Blog
{
    const STATUS_ACTIVE = 1;
    const STATUS_PENDING = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BlogTranslation", mappedBy="blog", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $blogTranslations;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="BlogHasTags", mappedBy="blog", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $blogHasTags;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Image", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $image;

    public function __construct()
    {
        $this->blogTranslations = new ArrayCollection();
        $this->blogHasTags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|BlogTranslation[]
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

    public function getBlogTranslationByLocale(string $locale): ?BlogTranslation
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

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection|BlogHasTags[]
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

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(Image $image): self
    {
        $this->image = $image;

        return $this;
    }
}
