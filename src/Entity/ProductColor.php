<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductColorRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(columns={"main_slug", "locale"})})
 */
class ProductColor
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=7)
     * @Assert\NotBlank(message="field.not_blank", groups={"SetColor"})
     */
    private $hex;

    /**
     * @ORM\Column(type="string", length=15)
     * @Assert\NotBlank(message="field.not_blank", groups={"SetColor"})
     * @Assert\Length(maxMessage="field.max_length", groups={"SetColor"}, max="15")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"name"}, updatable=false)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mainSlug;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $locale;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHex(): ?string
    {
        return $this->hex;
    }

    public function setHex(string $hex): self
    {
        $this->hex = $hex;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }
}
