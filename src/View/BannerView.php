<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Banner;
use App\Repository\ImageRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Routing\RouterInterface;

final class BannerView
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly ImageView $imageView,
        private readonly ImageRepository $imageRepository,
        private readonly array $locales,
    ) {}

    public function view(Banner $banner): array
    {
        $images = $this->getImages($banner, ['desktop' => 'menu_banner', 'mobile' => 'menu_banner']);

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

//        $trans = $banner->getByLocale($locale);
//        $filter = in_array($banner->getPosition(), [1,4]) ? 'home_banner_side' : 'home_banner_center';
//
//        $bannerView = $this->links($banner, $locale, $filter);
//
//        $bannerView['description'] = explode(PHP_EOL, $trans->getDescription());
//
//        return $bannerView;
    }

//    public function menuView(Banner $banner, string $locale): array
//    {
//        $banner->getByLocale($locale);
//
//        return $this->links($banner, $locale, 'menu_banner');
//    }

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

//    private function image(Banner $banner, string $locale, string $filter): string
//    {
//        $imageName = $banner->getImage()->getName();
//
//        return $this->router->generate(
//                'app.image_show',
//                ['entity' => 'banner', 'name' => $imageName, 'filter' => $filter])
//        ;
//    }

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
