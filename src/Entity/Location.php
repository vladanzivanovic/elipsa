<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LocationRepository")
 */
class Location
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $lat;

    /**
     * @ORM\Column(type="string")
     */
    private $lng;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $workingTime;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $workingTimeWeekend;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $zipCode;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LocationTranslation", mappedBy="location", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $locationTranslations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LocationHasImages", mappedBy="location", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $locationHasImages;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $countryCode;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $countryLat;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $countryLng;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $countryNorthLat;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $countryNorthLng;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $countrySouthLat;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $countrySouthLng;

    public function __construct()
    {
        $this->locationTranslations = new ArrayCollection();
        $this->locationHasImages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLat(): ?string
    {
        return $this->lat;
    }

    public function setLat(string $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?string
    {
        return $this->lng;
    }

    public function setLng(string $lng): self
    {
        $this->lng = $lng;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getWorkingTime(): ?string
    {
        return $this->workingTime;
    }

    public function setWorkingTime(string $workingTime): self
    {
        $this->workingTime = $workingTime;

        return $this;
    }

    public function getWorkingTimeWeekend(): ?string
    {
        return $this->workingTimeWeekend;
    }

    public function setWorkingTimeWeekend(string $workingTimeWeekend): self
    {
        $this->workingTimeWeekend = $workingTimeWeekend;

        return $this;
    }

    public function getZipCode(): ?string
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
    public function getLocationTranslations(): Collection
    {
        return $this->locationTranslations;
    }

    public function addLocationTranslation(LocationTranslation $locationTranslation): self
    {
        if (!$this->locationTranslations->contains($locationTranslation)) {
            $this->locationTranslations[] = $locationTranslation;
            $locationTranslation->setLocation($this);
        }

        return $this;
    }

    public function removeLocationTranslation(LocationTranslation $locationTranslation): self
    {
        if ($this->locationTranslations->contains($locationTranslation)) {
            $this->locationTranslations->removeElement($locationTranslation);
            // set the owning side to null (unless already changed)
            if ($locationTranslation->getLocation() === $this) {
                $locationTranslation->setLocation(null);
            }
        }

        return $this;
    }

    /**
     * @param string $locale
     *
     * @return LocationTranslation
     */
    public function getByLocale(string $locale): LocationTranslation
    {
        $trans = $this->locationTranslations;

        $filteredTrans = $trans->filter(function ($locationTrans) use ($locale) {
            /** @var LocationTranslation $locationTrans */
            return $locationTrans->getLocale() === $locale;
        });

        return $filteredTrans->first();
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

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getCountryLat(): ?string
    {
        return $this->countryLat;
    }

    public function setCountryLat(string $countryLat): self
    {
        $this->countryLat = $countryLat;

        return $this;
    }

    public function getCountryLng(): ?string
    {
        return $this->countryLng;
    }

    public function setCountryLng(string $countryLng): self
    {
        $this->countryLng = $countryLng;

        return $this;
    }

    public function getCountryNorthLat(): ?string
    {
        return $this->countryNorthLat;
    }

    public function setCountryNorthLat(string $countryNorthLat): self
    {
        $this->countryNorthLat = $countryNorthLat;

        return $this;
    }

    public function getCountryNorthLng(): ?string
    {
        return $this->countryNorthLng;
    }

    public function setCountryNorthLng(string $countryNorthLng): self
    {
        $this->countryNorthLng = $countryNorthLng;

        return $this;
    }

    public function getCountrySouthLat(): ?string
    {
        return $this->countrySouthLat;
    }

    public function setCountrySouthLat(string $countrySouthLat): self
    {
        $this->countrySouthLat = $countrySouthLat;

        return $this;
    }

    public function getCountrySouthLng(): ?string
    {
        return $this->countrySouthLng;
    }

    public function setCountrySouthLng(string $countrySouthLng): self
    {
        $this->countrySouthLng = $countrySouthLng;

        return $this;
    }
}
