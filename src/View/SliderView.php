<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Slider;
use App\Helper\ConstantsHelper;
use App\Repository\ImageRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Routing\RouterInterface;

final class SliderView
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly ImageRepository $imageRepository,
        private readonly ImageView $imageView,
        private readonly array $locales,
    ) {}

    /**
     * @throws NonUniqueResultException
     */
    public function siteView(Slider $slider, $locale): array
    {
        $sliderTrans = $slider->getByLocale($locale);
        $sliderImageName = $slider->getImage()->getName();
        $mobileImage = $this->imageRepository->getRelatedImage($sliderImageName);

        preg_match_all('%(<p[^>]*>.*?</p>)%im', $sliderTrans->getDescription(), $descriptions);

        return [
            'id' => $slider->getId(),
            'button_link' => $sliderTrans->getButtonLink(),
            'descriptions' => $descriptions[0],
            'image_link' => $this->router->generate('app.image_show', ['entity' => 'slider', 'name' => $sliderImageName, 'filter' => 'site_slider']),
            'mobile_image_link' => $this->router->generate('app.image_show', ['entity' => 'slider', 'name' => $mobileImage->getName(), 'filter' => 'site_slider_mobile']),
        ];
    }

    /**
     * @throws NonUniqueResultException
     */
    public function editView(Slider $slider): array
    {
        $images = $this->getImages($slider, ['desktop' => 'tmp_image_thumb', 'mobile' => 'tmp_image_thumb']);

        $images['desktop'] = [$images['desktop']];
        $images['mobile'] = [$images['mobile']];

        $view = [
            'id' => $slider->getId(),
            'position' => $slider->getPosition(),
            'is_active' => $slider->getIsActive(),
            'translations' => $this->getTranslationValues($slider),
            'available_countries' => $slider->getAvailableCountries(),
            'media' => [
                'images' => $images,
            ]
        ];

        return $view;
    }

    private function getTranslationValues(Slider $slider): array
    {
        $translations = [];

        foreach ($this->locales as $locale) {
            $trans = $slider->getByLocale($locale);

            $translations[$locale] = [
                'id' => $trans?->getId(),
                'description' => $trans?->getDescription(),
                'button_link' => $trans?->getButtonLink(),
            ];
        }

        return $translations;
    }

    /**
     * @throws NonUniqueResultException
     */
    private function getImages(Slider $slider, array $filters): array
    {
        $desktopImage = $slider->getImage();
        $mobileImage = $this->imageRepository->getRelatedImage($desktopImage->getName());

        $view = [
            'desktop' => $this->imageView->view($desktopImage, 'slider', $filters['desktop']),
            'mobile' => $this->imageView->view($mobileImage, 'slider', $filters['mobile'])
        ];

        return $view;
    }
}
