<?php

declare(strict_types=1);

namespace App\Formatter\Options;

use App\Repository\LocationRepository;
use App\View\LocationView;

final class LocationOptionsFormatter
{
    public function __construct(
        private readonly LocationView $locationView,
        private readonly LocationRepository $locationRepository,
        private readonly string $defaultLocale
    ) {}

    public function format(string $country, string $locale = null): array
    {
        $locations = $this->locationRepository->getForOptions($country, $locale ?? $this->defaultLocale);

        $formattedLocations = [];

        foreach ($locations as $location) {
            $formattedLocations[] = $this->locationView->getForOptions($location, $locale ?? $this->defaultLocale);
        }

        return $formattedLocations;
    }
}
