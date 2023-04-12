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
    private RouterInterface $router;

    private ImageRepository $imageRepository;

    public function __construct(
        RouterInterface $router,
        ImageRepository $imageRepository
    ) {

        $this->router = $router;
        $this->imageRepository = $imageRepository;
    }

    /**
     * @throws \ReflectionException
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
}
