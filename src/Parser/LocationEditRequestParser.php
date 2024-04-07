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

    private \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $parameterBag;

    private \App\Services\LocationImageService $imageService;

    private \App\Repository\BannerRepository $bannerRepository;

    private array $locales;

    public function __construct(
        ParameterBagInterface $parameterBag,
        LocationImageService $imageService,
        BannerRepository $bannerRepository,
        string $locales
    ) {
        $this->parameterBag = $parameterBag;
        $this->imageService = $imageService;
        $this->bannerRepository = $bannerRepository;
        $this->locales = explode('|', $locales);
    }

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
            $trans = new LocationTranslation();

            if (null !== $location->getId()) {
                $trans = $location->getByLocale($locale);
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
