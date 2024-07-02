<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Image;
use App\Entity\Slider;
use App\Repository\ImageRepository;
use App\View\SliderView;
use Symfony\Component\Routing\RouterInterface;

final class SliderEditResponseFormatter
{
    use ImageTrait;

    public function __construct(
        private readonly RouterInterface $router,
        private readonly ImageRepository $imageRepository,
        private readonly SliderView $sliderView,
    ) {}

    public function formatResponse(Slider $slider = null): array
    {
        $view = $this->sliderView->editView($slider);

        return [
            'payload' => $view,
        ];

        $rsTrans = $slider->getByLocale('rs');
        $enTrans = $slider->getByLocale('en');

        $images = $this->getImages($slider);

        $desktopImage = $this->imagesFormatter($this->router, [$images['desktop']], 'slider');

        $imagesArray = ['desktop' => $desktopImage];


        if (isset($images['mobile'])) {
            $imagesArray['mobile'] = $this->imagesFormatter($this->router, [$images['mobile']], 'slider');
        }

        return [
            'rs_description' => $rsTrans->getDescription(),
            'rs_link' => $rsTrans->getButtonLink(),
            'en_description' => $enTrans->getDescription(),
            'en_link' => $enTrans->getButtonLink(),
            'selectedImages' => $imagesArray,
        ];
    }

    
    private function getImages(Slider $slider): array
    {
        $image = $slider->getImage();

        $mobileImage = $this->imageRepository->findOneBy(['parentImage' => $image->getName(), 'device' => Image::DEVICE_MOBILE]);

        $images = [
            'desktop' => [
                'id' => $image->getId(),
                'fileName' => $image->getName(),
                'isMain' => $image->getIsMain(),
            ],
        ];

        if (null !== $mobileImage) {
            $images['mobile'] = [
                'id' => $mobileImage->getId(),
                'fileName' => $mobileImage->getName(),
                'isMain' => $mobileImage->getIsMain(),
            ];
        }

        return $images;
    }
}
