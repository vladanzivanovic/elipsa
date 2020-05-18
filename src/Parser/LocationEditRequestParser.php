<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Location;
use App\Entity\LocationTranslation;
use App\Repository\BannerRepository;
use App\Services\LocationImageService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

final class LocationEditRequestParser
{
    use ParserTrait;

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * @var LocationImageService
     */
    private $imageService;

    /**
     * @var BannerRepository
     */
    private $bannerRepository;

    /**
     * @param ParameterBagInterface $parameterBag
     * @param LocationImageService  $imageService
     * @param BannerRepository      $bannerRepository
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        LocationImageService $imageService,
        BannerRepository $bannerRepository
    ) {
        $this->parameterBag = $parameterBag;
        $this->imageService = $imageService;
        $this->bannerRepository = $bannerRepository;
    }

    /**
     * @param ParameterBag  $bag
     * @param Location|null $location
     *
     * @return Location
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
            ->setWorkingTimeWeekend($bag->get('working_hours_weekend'))
            ->setCountryCode($bag->get('country_short_code'))
            ->setCountryLat($bag->get('country_lat'))
            ->setCountryLng($bag->get('country_lng'))
            ->setCountryNorthLat($bag->get('country_north_lat'))
            ->setCountryNorthLng($bag->get('country_north_lng'))
            ->setCountrySouthLat($bag->get('country_south_lat'))
            ->setCountrySouthLng($bag->get('country_south_lng'));

        $this->setLocale($bag, $location);

        $this->imageService->setImages($location, json_decode($bag->get('images'), true));

        return $location;
    }

    /**
     * @param ParameterBag $bag
     * @param Location     $location
     *
     * @return void
     */
    private function setLocale(ParameterBag $bag, Location $location): void
    {
        $locales = $this->setLanguageArray($this->parameterBag, $bag);

        foreach ($locales as $locale => $lagBag) {
            $trans = new LocationTranslation();

            if (null !== $location->getId()) {
                $trans = $location->getByLocale($locale);
            }

            $trans->setStreet($lagBag->get('street'))
                ->setCity($lagBag->get('city'))
                ->setCountry($lagBag->get('country'))
                ->setLocale($locale)
                ->setTitle($lagBag->get('title'))
                ->setShortDescription($lagBag->get('description'));

            $location->addLocationTranslation($trans);
        }
    }
}