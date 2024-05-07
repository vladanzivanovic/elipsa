<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: \App\Repository\CareerRepository::class)]
class Career
{
    use TimestampableEntity;

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

    #[ORM\OneToOne(targetEntity: \App\Entity\Image::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private $cv;

    #[ORM\Column(type: 'text', nullable: true)]
    private $accompanyingLetter;

    #[ORM\Column(type: 'datetime')]
    private $birthDate;

    #[ORM\Column(type: 'string', length: 255)]
    private $address;

    #[ORM\Column(type: 'string', length: 100)]
    private $city;

    #[ORM\Column(type: 'string', length: 50)]
    private $mobilePhone;

    #[ORM\Column(type: 'string', length: 255)]
    private $school;

    #[ORM\Column(type: 'string', length: 255)]
    private $schoolLevel;

    #[ORM\Column(type: 'string', length: 255)]
    private $schoolTitle;

    #[ORM\ManyToOne(targetEntity: \App\Entity\CareerDescription::class, inversedBy: 'careers')]
    #[ORM\JoinColumn(nullable: false)]
    private $position;

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

    public function getCv(): ?Image
    {
        return $this->cv;
    }

    public function setCv(?Image $cv): self
    {
        $this->cv = $cv;

        return $this;
    }

    public function getAccompanyingLetter(): ?string
    {
        return $this->accompanyingLetter;
    }

    public function setAccompanyingLetter(?string $accompanyingLetter): self
    {
        $this->accompanyingLetter = $accompanyingLetter;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getMobilePhone(): ?string
    {
        return $this->mobilePhone;
    }

    public function setMobilePhone(string $mobilePhone): self
    {
        $this->mobilePhone = $mobilePhone;

        return $this;
    }

    public function getSchool(): ?string
    {
        return $this->school;
    }

    public function setSchool(string $school): self
    {
        $this->school = $school;

        return $this;
    }

    public function getSchoolLevel(): ?string
    {
        return $this->schoolLevel;
    }

    public function setSchoolLevel(string $schoolLevel): self
    {
        $this->schoolLevel = $schoolLevel;

        return $this;
    }

    public function getSchoolTitle(): ?string
    {
        return $this->schoolTitle;
    }

    public function setSchoolTitle(string $schoolTitle): self
    {
        $this->schoolTitle = $schoolTitle;

        return $this;
    }

    public function getPosition(): ?CareerDescription
    {
        return $this->position;
    }

    public function setPosition(?CareerDescription $position): self
    {
        $this->position = $position;

        return $this;
    }
}
