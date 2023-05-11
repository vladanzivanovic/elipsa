<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TagsRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(columns={"main_slug", "locale"})})
 * @ORM\Table(indexes={@ORM\Index(columns={"label"}, flags={"fulltext"})})
 */
class Tags
{
    public const TYPE_PRODUCT = 1;
    public const TYPE_BLOG = 2;

    public const PRODUCT_TYPE_SEASON = 'season';

    public const PRODUCT_TYPE_COLLECTION = 'collection';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="field.not_blank", groups={"SetTag"})
     * @Assert\Length(maxMessage="field.max_length", groups={"SetTag"}, max="50")
     */
    private string $label;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private string $locale;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"label"}, updatable=false)
     */
    private string $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $mainSlug;

    /**
     * @ORM\Column(type="integer")
     */
    private int $relatedType;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private string $productType;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getMainSlug(): ?string
    {
        return $this->mainSlug;
    }

    public function setMainSlug(string $mainSlug): self
    {
        $this->mainSlug = $mainSlug;

        return $this;
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

    public function getProductType(): string
    {
        return $this->productType;
    }

    public function setProductType(string $productType): void
    {
        $this->productType = $productType;
    }
}
