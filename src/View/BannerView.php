<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Banner;
use App\Repository\ImageRepository;
use Doctrine\ORM\NonUniqueResultException;

final class BannerView
{
    public function __construct(
        private readonly ImageView $imageView,
        private readonly ImageRepository $imageRepository,
        private readonly array $locales,
    ) {}

    /**
     * @throws NonUniqueResultException
     */
    public function view(Banner $banner, array $imageFilter): array
    {
        $images = $this->getImages($banner, $imageFilter);

        $view = [
            'id' => $banner->getId(),
            'type' => $banner->getType(),
            'is_active' => $banner->getIsActive(),
            'position' => $banner->getPosition(),
            'translations' => $this->getTranslationValues($banner),
            'media' => [
                'images' => $images,
            ]
        ];

        return $view;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function editView(Banner $banner): array
    {
        $images = $this->getImages($banner, ['desktop' => 'tmp_image_thumb', 'mobile' => 'tmp_image_thumb']);

        $images['desktop'] = [$images['desktop']];

        if (isset($images['mobile'])) {
            $images['mobile'] = [$images['mobile']];
        }

        $view = [
            'id' => $banner->getId(),
            'type' => $banner->getType(),
            'is_active' => $banner->getIsActive(),
            'position' => $banner->getPosition(),
            'translations' => $this->getTranslationValues($banner),
            'available_countries' => $banner->getAvailableCountries(),
            'media' => [
                'images' => $images,
            ]
        ];

        return $view;
    }

    private function getTranslationValues(Banner $banner): array
    {
        $translations = [];

        foreach ($this->locales as $locale) {
            $trans = $banner->getByLocale($locale);

            $translations[$locale] = [
                'id' => $trans?->getId(),
                'button_link' => $trans?->getButtonLink(),
                'button_text' => $trans?->getButtonText(),
                'description' => $trans?->getDescription(),
            ];
        }

        return $translations;
    }

    /**
     * @throws NonUniqueResultException
     */
    private function getImages(Banner $banner, array $filters): array
    {
        $desktopImage = $banner->getImage();
        $mobileImage = $this->imageRepository->getRelatedImage($desktopImage->getName());

        $view = [
            'desktop' => $this->imageView->view($desktopImage, 'banner', $filters['desktop']),
        ];

        if (null !== $mobileImage) {
            $view['mobile'] = $this->imageView->view($mobileImage, 'banner', $filters['mobile']);
        }

        return $view;
    }
}
