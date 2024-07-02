<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Entity\Location;
use App\View\LocationView;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\RouterInterface;

final class LocationPageResponseFormatter
{
    public function __construct(
        private readonly LocationView $locationView,
        private readonly string $defaultLocale
    ) {}

    /**
     * @param Location[] $locations
     *
     * @return array<string, array<array<string|int, mixed>>>
     */
    public function formatResponse(array $locations, string $locale): array
    {
        $formattedResponse = [];

        foreach ($locations as $location) {
            $trans = $location->getByLocale($locale);

            if (null === $trans) {
                $trans = $location->getByLocale($this->defaultLocale);
            }

            $key = in_array($trans->getCity(), ['Beograd', 'Belgrade'], true) ?
                $trans->getCity() : $trans->getCountry();

            $formattedResponse[$key][] = $this->locationView->view($location);
        }

        return ['payload' => $formattedResponse];
    }
}
