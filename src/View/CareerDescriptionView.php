<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\CareerDescription;
use App\Entity\CareerDescriptionTranslation;
use App\Repository\ImageRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Routing\RouterInterface;

final class CareerDescriptionView
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly ImageRepository $imageRepository,
        private readonly ImageView $imageView,
        private readonly array $locales,
    ) {}

    public function editView(CareerDescription $careerDescription): array
    {
        $view = $this->view($careerDescription, ['desktop' => 'tmp_image_thumb']);

        return $view;
    }

    public function view(CareerDescription $careerDescription, array $imageFilter): array
    {
        $images = $this->getImages($careerDescription, $imageFilter);

        $view = [
            'id' => $careerDescription->getId(),
            'available_countries' => $careerDescription->getAvailableCountries(),
            'activation_date' => $careerDescription->getActivationDate(),
            'status' => $careerDescription->getStatus(),
            'media' => [
                'images' => $images,
            ]
        ];

        $view['translations'] = $this->getTranslationValues($careerDescription);

        return $view;
    }

    private function getTranslationValues(CareerDescription $careerDescription): array
    {
        $translations = [];

        foreach ($this->locales as $locale) {
            /** @var CareerDescriptionTranslation $trans */
            $trans = $careerDescription->getByLocale($locale);

            $translations[$locale] = [
                'id' => $trans?->getId(),
                'title' => $trans?->getTitle(),
                'description' => $trans?->getDescription(),
            ];
        }

        return $translations;
    }

    /**
     * @throws NonUniqueResultException
     */
    private function getImages(CareerDescription $careerDescription, array $filters): array
    {
        $image = $careerDescription->getImage();

        $view = [$this->imageView->view($image, 'job', $filters['desktop'])];

        return $view;
    }
}
