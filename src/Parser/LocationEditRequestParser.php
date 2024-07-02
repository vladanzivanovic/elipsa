<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Location;
use App\Entity\LocationTranslation;
use App\Services\LocationImageService;
use Symfony\Component\HttpFoundation\ParameterBag;

final class LocationEditRequestParser
{
    public function __construct(
        private readonly LocationImageService $imageService,
        private readonly array $locales
    ) {}

    /**
     * @param Location|null $location
     * @throws \Doctrine\ORM\ORMException
     */
    public function parse(ParameterBag $bag, Location $location = null): Location
    {
        if (!$location instanceof Location) {
            $location = new Location();
        }

        $location->setTelephone($bag->get('telephone'))
            ->setEmail($bag->get('email'))
            ->setZipCode($bag->get('zip_code'))
            ->setLat($bag->get('lat'))
            ->setLng($bag->get('lng'))
            ->setWorkingTime($bag->get('working_hours'))
            ->setSaturday($bag->get('working_hours_saturday'))
            ->setSunday($bag->get('working_hours_sunday'));

        $this->setLocale($bag, $location);

        $this->imageService->setImages($location, json_decode($bag->get('images'), true));

        return $location;
    }

    private function setLocale(ParameterBag $bag, Location $location): void
    {
        foreach ($this->locales as $locale) {
            $transCollection = $bag->all($locale);

            $trans = $location->getByLocale($locale);

            if (null === $trans) {
                $trans = new LocationTranslation();
            }

            $trans->setStreet($transCollection['street'])
                ->setCity($transCollection['city'])
                ->setCountry($transCollection['country'])
                ->setLocale($locale)
                ->setTitle($transCollection['title'])
                ->setShortDescription($transCollection['description']);

            $location->addLocationTranslation($trans);
        }
    }
}
