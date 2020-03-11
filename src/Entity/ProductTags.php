<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductTagsRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(columns={"main_slug", "locale"})})
 */
class ProductTags
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="field.not_blank", groups={"SetTag"})
     * @Assert\Length(maxMessage="field.max_length", groups={"SetTag"}, max="50")
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $locale;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"label"}, updatable=false)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mainSlug;

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
}
