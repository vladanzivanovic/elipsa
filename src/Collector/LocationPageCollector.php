<?php

declare(strict_types=1);

namespace App\Collector;

use App\Repository\LocationRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class LocationPageCollector
{
    /**
     * @var LocationRepository
     */
    private $locationRepository;

    /**
     * @param LocationRepository    $locationRepository
     */
    public function __construct(
        LocationRepository $locationRepository
    ) {
        $this->locationRepository = $locationRepository;
    }

    /**
     * @param string $locale
     * @param string $countryCode
     *
     * @return array
     */
    public function collect(string $locale, string $countryCode = 'RS'): array
    {
        return [
            'locations' => $this->locationRepository->getList($locale, $countryCode),
            'countries' => $this->locationRepository->getCountryList($locale),
        ];
    }
}