<?php

namespace App\Entity;

use App\Entity\Resources\EntityInterface;
use App\Entity\Resources\LocaleInterface;
use App\Entity\Resources\LocaleTrait;
use App\Entity\Resources\ResourceTrait;
use App\Repository\LocationTranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: LocationTranslationRepository::class)]
class LocationTranslation implements EntityInterface, LocaleInterface
{
    use ResourceTrait;
    use LocaleTrait;

    #[ORM\Column(type: 'string', length: 100)]
    private string $title;

    #[ORM\Column(type: 'string', length: 100)]
    #[Gedmo\Slug(fields: ['title'], updatable: false)]
    private string $slug;

    #[ORM\Column(type: 'string', length: 100)]
    private string $street;

    #[ORM\Column(type: 'string', length: 100)]
    private string $city;

    #[ORM\Column(type: 'string', length: 100)]
    private string $country;

    #[ORM\ManyToOne(targetEntity: Location::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(nullable: false)]
    private Location $location;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $shortDescription = null;

    public function getTitle(): null|string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): null|string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getStreet(): null|string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getCity(): null|string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): null|string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getLocation(): null|Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getShortDescription(): null|string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }
}
