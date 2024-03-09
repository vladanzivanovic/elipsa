<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Entity\Location;
use App\View\LocationView;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\RouterInterface;

final class LocationPageResponseFormatter
{
    use FormatterTrait;

    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var ParameterBagInterface
     */
    private $bag;

    private LocationView $locationView;

    /**
     * @param RouterInterface       $router
     * @param ParameterBagInterface $bag
     */
    public function __construct(
        RouterInterface $router,
        ParameterBagInterface $bag,
        LocationView $locationView
    ) {
        $this->router = $router;
        $this->bag = $bag;
        $this->locationView = $locationView;
    }

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

            $key = in_array($trans->getCity(), ['Beograd', 'Belgrade'], true) ?
                $trans->getCity() : $trans->getCountry();

            $formattedResponse[$key][] = $this->locationView->view($location);
        }

        return ['payload' => $formattedResponse];
    }
}
