<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Banner;
use App\Entity\Location;
use App\Repository\ImageRepository;
use Symfony\Component\Routing\RouterInterface;

final class LocationEditResponseFormatter
{
    use ImageTrait;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ImageRepository
     */
    private $imageRepository;

    /**
     * @param RouterInterface $router
     * @param ImageRepository $imageRepository
     */
    public function __construct(
        RouterInterface $router,
        ImageRepository $imageRepository
    ) {
        $this->router = $router;
        $this->imageRepository = $imageRepository;
    }

    /**
     * @param Location $location
     *
     * @return array
     */
    public function formatResponse(Location $location): array
    {
        $rsTrans = $location->getByLocale('rs');
        $enTrans = $location->getByLocale('en');

        return [
            'rs_title' => $rsTrans->getTitle(),
            'rs_street' => $rsTrans->getStreet(),
            'rs_city' => $rsTrans->getCity(),
            'rs_country' => $rsTrans->getCountry(),
            'zip_code' => $location->getZipCode(),
            'rs_description' => $rsTrans->getShortDescription(),
            'en_description' => $enTrans->getShortDescription(),
            'en_title' => $enTrans->getTitle(),
            'en_street' => $enTrans->getStreet(),
            'en_city' => $enTrans->getCity(),
            'en_country' => $enTrans->getCountry(),
            'working_hours' => $location->getWorkingTime(),
            'working_hours_weekend' => $location->getWorkingTimeWeekend(),
            'email' => $location->getEmail(),
            'telephone' => $location->getTelephone(),
            'lat' => $location->getLat(),
            'lng' => $location->getLng(),
            'selectedImages' => $this->imagesFormatter($this->router, $this->imageRepository->getByLocation($location), 'location'),
        ];
    }
}