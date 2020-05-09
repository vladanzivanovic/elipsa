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
     * @ORM\Column(type="float")
     */
    private $lat;

    /**
     * @ORM\Column(type="float")
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

    public function __construct()
    {
        $this->locationTranslations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(float $lng): self
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
}
