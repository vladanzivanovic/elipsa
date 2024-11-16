<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Slider;
use App\Entity\SliderTranslation;
use App\Repository\SliderRepository;
use App\Repository\SliderTranslationRepository;
use App\Request\Dto\Admin\SliderEditRequestDto;
use App\Services\SliderImageService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

final class SliderEditRequestParser
{
    public function __construct(
        private readonly SliderImageService $imageService,
        private readonly SliderRepository $sliderRepository,
        private readonly SliderTranslationRepository $translationRepository,
        private readonly array $locales,
    ) {}

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function parse(SliderEditRequestDto $sliderEditRequestDto, Slider $slider = null): Slider
    {
        if (!$slider instanceof Slider) {
            $lastPosition = $this->sliderRepository->getLastPosition();
            $newPosition = count($lastPosition) === 1 ? $lastPosition[0]['position'] + 1 : 1;

            $slider = new Slider();
            $slider->setIsActive(false);
            $slider->setPosition($newPosition);
        }

        $slider->setAvailableCountries($sliderEditRequestDto->availableCountries);

        $this->setLocale($sliderEditRequestDto->translations, $slider);

        foreach ($sliderEditRequestDto->images as $device => $images) {
            $this->imageService->setImages($slider, $images, $device);
        }

        return $slider;
    }

    private function setLocale(array $translations, Slider $slider): void
    {
        foreach ($this->locales as $locale) {
            $transCollection = $translations[$locale] ?? null;

            if (null === $transCollection) {
                continue;
            }

            $trans = $this->translationRepository->findOneBy(['slider' => $slider, 'locale' => $locale]);

            if (null === $trans) {
                $trans = new SliderTranslation();
            }

            $trans->setDescription($transCollection['description'] ?? null);
            $trans->setButtonLink($transCollection['link']);
            $trans->setLocale($locale);

            $slider->addSliderTranslation($trans);
        }
    }
}
