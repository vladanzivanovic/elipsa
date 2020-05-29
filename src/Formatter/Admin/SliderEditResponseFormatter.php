<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Slider;
use Symfony\Component\Routing\RouterInterface;

final class SliderEditResponseFormatter
{
    use ImageTrait;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * SliderEditResponseFormatter constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(
        RouterInterface $router
    ) {
        $this->router = $router;
    }

    /**
     * @param Slider $slider
     *
     * @return array
     */
    public function formatResponse(Slider $slider): array
    {
        $rsTrans = $slider->getByLocale('rs');
        $enTrans = $slider->getByLocale('en');

        return [
            'rs_description' => $rsTrans->getDescription(),
            'rs_button' => $rsTrans->getButtonText(),
            'rs_link' => $rsTrans->getButtonLink(),
            'en_description' => $enTrans->getDescription(),
            'en_button' => $enTrans->getButtonText(),
            'en_link' => $enTrans->getButtonLink(),
            'position' => $slider->getTextPosition(),
            'selectedImages' => $this->imagesFormatter($this->router, [$this->getImage($slider)], 'slider'),
        ];
    }

    /**
     * @param Slider $slider
     *
     * @return array
     */
    private function getImage(Slider $slider): array
    {
        $image = $slider->getImage();

        return [
            'id' => $image->getId(),
            'fileName' => $image->getName(),
            'isMain' => $image->getIsMain(),
        ];
    }
}