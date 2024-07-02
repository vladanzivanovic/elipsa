<?php

declare(strict_types=1);

namespace App\Collector;

use App\Repository\LocationRepository;

final class LocationPageCollector
{
    private LocationRepository $locationRepository;

    public function __construct(
        LocationRepository $locationRepository
    ) {
        $this->locationRepository = $locationRepository;
    }

    
    public function collect(): array
    {
        return $this->locationRepository->findAll();
    }
}
