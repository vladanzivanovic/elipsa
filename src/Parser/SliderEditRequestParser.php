<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Image;
use App\Entity\Slider;
use App\Entity\SliderTranslation;
use App\Repository\SliderRepository;
use App\Services\SliderImageService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

final class SliderEditRequestParser
{
    use ParserTrait;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    /**
     * @var SliderImageService
     */
    private $imageService;
    /**
     * @var SliderRepository
     */
    private $sliderRepository;

    /**
     * SliderEditRequestParser constructor.
     *
     * @param ParameterBagInterface $parameterBag
     * @param SliderImageService    $imageService
     * @param SliderRepository      $sliderRepository
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        SliderImageService $imageService,
        SliderRepository $sliderRepository
    ) {
        $this->parameterBag = $parameterBag;
        $this->imageService = $imageService;
        $this->sliderRepository = $sliderRepository;
    }

    /**
     * @param ParameterBag $bag
     * @param Slider|null  $slider
     *
     * @return Slider
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     */
    public function parse(ParameterBag $bag, Slider $slider = null): Slider
    {
        if (!$slider instanceof Slider) {
            $lastPosition = $this->sliderRepository->getLastPosition();
            $newPosition = count($lastPosition) === 1 ? $lastPosition[0]['position'] + 1 : 1;

            $slider = new Slider();
            $slider->setIsActive(false);
            $slider->setPosition($newPosition);
        }

        $slider->setTextPosition($bag->getInt('position'));

        $this->setLocale($bag, $slider);

        $this->imageService->setImages($slider, json_decode($bag->get('images'), true), Image::DEVICE_DESKTOP);
        $this->imageService->setImages($slider, json_decode($bag->get('images_mobile'), true), Image::DEVICE_MOBILE);

        return $slider;
    }

    private function setLocale(ParameterBag $bag, Slider $slider)
    {
        $locales = $this->setLanguageArray($this->parameterBag, $bag);

        foreach ($locales as $locale => $lagBag) {
            $trans = new SliderTranslation();

            if (null !== $slider->getId()) {
                $trans = $slider->getByLocale($locale);
            }

            $trans->setDescription($lagBag->get('description'));
            $trans->setButtonText($lagBag->get('button'));
            $trans->setButtonLink($lagBag->get('link'));
            $trans->setLocale($locale);

            $slider->addSliderTranslation($trans);
        }
    }
}