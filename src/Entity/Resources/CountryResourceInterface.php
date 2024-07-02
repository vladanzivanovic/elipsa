<?php

declare(strict_types=1);

namespace App\Entity\Resources;

interface CountryResourceInterface
{
    public function getAvailableCountries(): array;

    public function hasByCountryCode(string $countryCode): bool;
}
