<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Resources\StatusInterface;
use App\Entity\SliderText;
use App\Entity\SliderTextTranslation;
use App\Repository\SliderTextTranslationRepository;
use App\Request\Dto\Admin\SliderTextEditRequestDto;

final class SliderTextEditRequestParser
{
    public function __construct(
        private readonly SliderTextTranslationRepository $translationRepository,
        private readonly array $locales,
    ) {}

    public function parse(SliderTextEditRequestDto $sliderTextEditRequestDto, SliderText $sliderText = null): SliderText
    {
        if (!$sliderText instanceof SliderText) {
            $sliderText = new SliderText();
            $sliderText->setStatus(StatusInterface::STATUS_PENDING);
        }

        $sliderText->setPosition($sliderTextEditRequestDto->position);
        $sliderText->setAvailableCountries($sliderTextEditRequestDto->availableCountries);

        $this->setLocale($sliderTextEditRequestDto->translations, $sliderText);

        return $sliderText;
    }

    private function setLocale(array $translations, Slidertext $sliderText): void
    {
        $sliderText->getSliderTextTranslations()->clear();

        foreach ($this->locales as $locale) {

            if (!isset($translations[$locale])) {
                continue;
            }

            $transCollection = $translations[$locale];
            $trans = $this->translationRepository->findOneBy(['sliderText' => $sliderText, 'locale' => $locale]);

            if (null === $trans) {
                $trans = new SliderTextTranslation();
            }

            $trans->setDescription($transCollection['description'] ?? null);
            $trans->setTitle($transCollection['title']);
            $trans->setLink($transCollection['link']);
            $trans->setLocale($locale);

            $sliderText->addSliderTextTranslation($trans);
        }
    }
}
