<?php

namespace App\Entity;

use App\Repository\CatalogueHasImagesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CatalogueHasImagesRepository::class)
 */
class CatalogueHasImages
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Image::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=Catalogue::class, inversedBy="catalogueHasImages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $catalogue;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCatalogue(): ?Catalogue
    {
        return $this->catalogue;
    }

    public function setCatalogue(?Catalogue $catalogue): self
    {
        $this->catalogue = $catalogue;

        return $this;
    }
}
