<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Image;
use App\Entity\Slider;
use App\Entity\SliderText;
use App\Entity\SliderTextTranslation;
use App\Repository\ImageRepository;
use Symfony\Component\Routing\RouterInterface;

final class SliderTextEditResponseFormatter
{
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
     * @param SliderText $sliderText
     *
     * @return array
     */
    public function formatResponse(SliderText $sliderText): array
    {
        $rsTrans = $sliderText->getByLocale('rs');
        $enTrans = $sliderText->getByLocale('en');

        return [
            'rs_description' => $rsTrans->getDescription(),
            'rs_link' => $rsTrans->getLink(),
            'en_description' => $enTrans->getDescription(),
            'en_link' => $enTrans->getLink(),
        ];
    }
}