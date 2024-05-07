<?php

declare(strict_types=1);

namespace App\Entity;

interface CountryResourceInterface
{
    public function getAvailableCountries(): array;
}
