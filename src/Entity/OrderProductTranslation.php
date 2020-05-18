<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderProductTranslationRepository")
 */
class OrderProductTranslation
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
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $locale;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\OrderProduct", inversedBy="orderProductTranslations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $orderProduct;

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

    public function getOrderProduct(): ?OrderProduct
    {
        return $this->orderProduct;
    }

    public function setOrderProduct(?OrderProduct $orderProduct): self
    {
        $this->orderProduct = $orderProduct;

        return $this;
    }
}
