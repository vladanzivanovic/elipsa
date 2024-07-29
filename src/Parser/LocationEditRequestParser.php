<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Location;
use App\Entity\LocationTranslation;
use App\Request\Dto\Admin\LocationEditRequestDto;
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
    public function parse(LocationEditRequestDto $locationEditRequestDto, Location $location = null): Location
    {
        if (!$location instanceof Location) {
            $location = new Location();
        }

        $location->setTelephone($locationEditRequestDto->telephone)
            ->setEmail($locationEditRequestDto->email)
            ->setZipCode($locationEditRequestDto->zipCode)
            ->setLat($locationEditRequestDto->lat)
            ->setLng($locationEditRequestDto->lng)
            ->setWorkingTime($locationEditRequestDto->workingHours)
            ->setSaturday($locationEditRequestDto->workingHoursSaturday)
            ->setSunday($locationEditRequestDto->workingHoursSunday)
            ->setAvailableCountries($locationEditRequestDto->availableCountries);

        $this->setLocale($locationEditRequestDto, $location);

        $this->imageService->setImages($location, $locationEditRequestDto->images);

        return $location;
    }

    private function setLocale(LocationEditRequestDto $locationEditRequestDto, Location $location): void
    {
        foreach ($this->locales as $locale) {
            $transCollection = $locationEditRequestDto->translations[$locale] ?? null;

            if (null === $transCollection) {
                continue;
            }

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
