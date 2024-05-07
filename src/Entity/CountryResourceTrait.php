<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

trait CountryResourceTrait
{
    #[ORM\Column(type: 'simple_array')]
    private array $availableCountries;

    public function setAvailableCountries(array $availableCountries): void
    {
        $this->availableCountries = $availableCountries;
    }

    public function getAvailableCountries(): array
    {
        return $this->availableCountries;
    }
}
