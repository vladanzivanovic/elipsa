<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Image;
use App\Entity\Slider;
use App\Entity\SliderTranslation;
use App\Repository\SliderRepository;
use App\Repository\SliderTranslationRepository;
use App\Services\SliderImageService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

final class SliderEditRequestParser
{
    use ParserTrait;

    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
        private readonly SliderImageService $imageService,
        private readonly SliderRepository $sliderRepository,
        private readonly SliderTranslationRepository $translationRepository,
        private readonly array $locales,
    ) {}

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
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

        $this->setLocale($bag, $slider);

        $this->imageService->setImages($slider, json_decode($bag->get('images'), true), Image::DEVICE_DESKTOP);
        $this->imageService->setImages($slider, json_decode($bag->get('images_mobile'), true), Image::DEVICE_MOBILE);

        return $slider;
    }

    private function setLocale(ParameterBag $bag, Slider $slider): void
    {
        foreach ($this->locales as $locale) {
            $transCollection = $bag->all($locale);
            $trans = $this->translationRepository->findOneBy(['slider' => $slider, 'locale' => $locale]);


            if (null === $trans) {
                $trans = new SliderTranslation();
            }

            $trans->setDescription($transCollection['description']);
            $trans->setButtonLink($transCollection['link']);
            $trans->setLocale($locale);

            $slider->addSliderTranslation($trans);
        }
    }
}
