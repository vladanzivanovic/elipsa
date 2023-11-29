<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Location;
use App\Formatter\Options\CountryOptionsFormatter;
use App\Repository\ImageRepository;
use App\View\LocationView;
use Symfony\Component\Routing\RouterInterface;

final class LocationEditResponseFormatter
{
    use ImageTrait;

    private RouterInterface $router;

    private ImageRepository $imageRepository;

    private LocationView $locationView;

    private CountryOptionsFormatter $countryOptionsFormatter;

    public function __construct(
        RouterInterface $router,
        ImageRepository $imageRepository,
        LocationView $locationView,
        CountryOptionsFormatter $countryOptionsFormatter
    ) {
        $this->router = $router;
        $this->imageRepository = $imageRepository;
        $this->locationView = $locationView;
        $this->countryOptionsFormatter = $countryOptionsFormatter;
    }

    public function formatResponse(?Location $location = null): array
    {
        $payload = [];

        $options = [
            'countries' => $this->countryOptionsFormatter->format(),
        ];

        if (null !== $location) {
            $payload = $this->locationView->editView($location);
        }

        return [
            'payload' => $payload,
            'options' => $options,
        ];
    }
}
