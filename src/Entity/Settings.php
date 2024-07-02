<?php

namespace App\Entity;

use App\Entity\Resources\CountryResourceInterface;
use App\Entity\Resources\CountryResourceTrait;
use App\Entity\Resources\EntityInterface;
use App\Entity\Resources\ResourceTrait;
use App\Repository\SettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingsRepository::class)]
class Settings implements EntityInterface
{
    use ResourceTrait;

    #[ORM\Column(type: 'string', length: 100)]
    private string $name;

    #[ORM\Column(type: 'text')]
    private string $value;

    #[ORM\Column(type: 'string', length: 100)]
    private string $slug;

    #[ORM\Column(type: 'string', length: 2, nullable: true)]
    private null|string $locale;

    #[ORM\Column(type: 'string', length: 15)]
    private string $inputType;

    #[ORM\Column(length: 2, nullable: true)]
    private null|string $country = null;

    public function getName(): null|string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): null|string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

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

    public function getLocale(): null|string
    {
        return $this->locale;
    }

    public function setLocale(null|string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getInputType(): null|string
    {
        return $this->inputType;
    }

    public function setInputType(?string $inputType): void
    {
        $this->inputType = $inputType;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }
}
