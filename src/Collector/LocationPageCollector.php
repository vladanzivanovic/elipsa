<?php

declare(strict_types=1);

namespace App\Collector;

use App\Entity\Location;
use App\Entity\Resources\StatusInterface;
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
        return $this->locationRepository->findBy(['status' => StatusInterface::STATUS_ACTIVE]);
    }
}
