<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: \App\Repository\CollaboratorRepository::class)]
class Collaborator
{
    use TimestampableEntity;

    public const SPACE_YES = 1;
    public const SPACE_NO = 2;

    public const LOCATION_MAIN_STREET = 1;
    public const LOCATION_SHOPPING_MALL = 2;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $firstName;

    #[ORM\Column(type: 'string', length: 100)]
    private $lastName;

    #[ORM\Column(type: 'string', length: 100)]
    private $email;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $phone;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $website;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $country;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $city;

    #[ORM\Column(type: 'smallint')]
    private $store;

    #[ORM\Column(type: 'smallint')]
    private $location;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $shoppingMall;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $spaceSize;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $numberOfFloors;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $address;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $zipCode;

    #[ORM\OneToOne(targetEntity: \App\Entity\Image::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private $presentation;

    #[ORM\OneToOne(targetEntity: \App\Entity\Image::class, cascade: ['persist', 'remove'])]
    private $plan;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getStore(): ?int
    {
        return $this->store;
    }

    public function setStore(int $store): self
    {
        $this->store = $store;

        return $this;
    }

    public function getLocation(): ?int
    {
        return $this->location;
    }

    public function setLocation(int $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getShoppingMall(): ?string
    {
        return $this->shoppingMall;
    }

    public function setShoppingMall(?string $shoppingMall): self
    {
        $this->shoppingMall = $shoppingMall;

        return $this;
    }

    public function getSpaceSize(): ?int
    {
        return $this->spaceSize;
    }

    public function setSpaceSize(?int $spaceSize): self
    {
        $this->spaceSize = $spaceSize;

        return $this;
    }

    public function getNumberOfFloors(): ?int
    {
        return $this->numberOfFloors;
    }

    public function setNumberOfFloors(?int $numberOfFloors): self
    {
        $this->numberOfFloors = $numberOfFloors;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getZipCode(): ?int
    {
        return $this->zipCode;
    }

    public function setZipCode(?int $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getPresentation(): ?Image
    {
        return $this->presentation;
    }

    public function setPresentation(?Image $presentation): self
    {
        $this->presentation = $presentation;

        return $this;
    }

    public function getPlan(): ?Image
    {
        return $this->plan;
    }

    public function setPlan(?Image $plan): self
    {
        $this->plan = $plan;

        return $this;
    }
}
