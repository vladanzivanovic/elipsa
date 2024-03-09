<?php

declare(strict_types=1);

namespace App\Formatter\Options;

use App\Repository\LocationRepository;
use App\View\LocationView;

final class LocationOptionsFormatter
{
    private LocationView $locationView;

    private LocationRepository $locationRepository;

    private string $defaultLocale;

    public function __construct(
        LocationView $locationView,
        LocationRepository $locationRepository,
        string $defaultLocale
    ) {

        $this->locationView = $locationView;
        $this->defaultLocale = $defaultLocale;
        $this->locationRepository = $locationRepository;
    }

    public function format($locale = null): array
    {
        $locations = $this->locationRepository->getForOptions($locale ?? $this->defaultLocale);

        $formattedLocations = [];

        foreach ($locations as $location) {
            $formattedLocations[] = $this->locationView->getForOptions($location, $locale ?? $this->defaultLocale);
        }

        return $formattedLocations;
    }
}
