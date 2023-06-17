<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Youtube
 *
 * @ORM\Entity(repositoryClass="App\Repository\YouTubeRepository")
 */
class Youtube
{
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private string $youtubeId;

    /**
     * @ORM\Column(type="string", length=250, nullable=false)
     */
    private string $title;

    /**
     * @ORM\Column(type="json", length=65535, nullable=false)
     */
    private array $images;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="youtubeIds")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private Product $product;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setYoutubeId($youtubeId): void
    {
        $this->youtubeId = $youtubeId;
    }

    public function getYoutubeId(): string
    {
        return $this->youtubeId;
    }

    public function setTitle($title): void
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setImages(array $images): void
    {
        $this->images = $images;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }
}
