<?php

namespace App\Entity;

use App\Entity\Resources\CountryResourceInterface;
use App\Entity\Resources\CountryResourceTrait;
use App\Entity\Resources\EntityInterface;
use App\Entity\Resources\LocaleTranslatorInterface;
use App\Entity\Resources\LocaleTranslatorTrait;
use App\Entity\Resources\ResourceTrait;
use App\Repository\LocationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocationRepository::class)]
class Location implements EntityInterface, CountryResourceInterface, LocaleTranslatorInterface
{
    use ResourceTrait;
    use CountryResourceTrait;
    use LocaleTranslatorTrait;


    #[ORM\Column(type: 'string')]
    private string $lat;

    #[ORM\Column(type: 'string')]
    private string $lng;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $workingTime;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $saturday = null;

    #[ORM\Column(type: 'string', length: 5)]
    private string $zipCode;

    #[ORM\OneToMany(mappedBy: 'location', targetEntity: LocationTranslation::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $translations;

    #[ORM\OneToMany(mappedBy: 'location', targetEntity: LocationHasImages::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $locationHasImages;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $sunday = null;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->locationHasImages = new ArrayCollection();
    }

    public function getLat(): null|string
    {
        return $this->lat;
    }

    public function setLat(string $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): null|string
    {
        return $this->lng;
    }

    public function setLng(string $lng): self
    {
        $this->lng = $lng;

        return $this;
    }

    public function getEmail(): null|string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTelephone(): null|string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getWorkingTime(): null|string
    {
        return $this->workingTime;
    }

    public function setWorkingTime(string $workingTime): self
    {
        $this->workingTime = $workingTime;

        return $this;
    }

    public function getSaturday(): null|string
    {
        return $this->saturday;
    }

    public function setSaturday(string $saturday): self
    {
        $this->saturday = $saturday;

        return $this;
    }

    public function getZipCode(): null|string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * @return Collection|LocationTranslation[]
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addLocationTranslation(LocationTranslation $locationTranslation): self
    {
        if (!$this->translations->contains($locationTranslation)) {
            $this->translations[] = $locationTranslation;
            $locationTranslation->setLocation($this);
        }

        return $this;
    }

    public function removeLocationTranslation(LocationTranslation $locationTranslation): self
    {
        if ($this->translations->contains($locationTranslation)) {
            $this->translations->removeElement($locationTranslation);
            // set the owning side to null (unless already changed)
            if ($locationTranslation->getLocation() === $this) {
                $locationTranslation->setLocation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|LocationHasImages[]
     */
    public function getLocationHasImages(): Collection
    {
        return $this->locationHasImages;
    }

    public function addLocationHasImage(LocationHasImages $locationHasImage): self
    {
        if (!$this->locationHasImages->contains($locationHasImage)) {
            $this->locationHasImages[] = $locationHasImage;
            $locationHasImage->setLocation($this);
        }

        return $this;
    }

    public function removeLocationHasImage(LocationHasImages $locationHasImage): self
    {
        if ($this->locationHasImages->contains($locationHasImage)) {
            $this->locationHasImages->removeElement($locationHasImage);
            // set the owning side to null (unless already changed)
            if ($locationHasImage->getLocation() === $this) {
                $locationHasImage->setLocation(null);
            }
        }

        return $this;
    }

    public function getSunday(): null|string
    {
        return $this->sunday;
    }

    public function setSunday(?string $sunday): self
    {
        $this->sunday = $sunday;

        return $this;
    }
}
