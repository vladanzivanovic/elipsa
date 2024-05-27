<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\SliderText;
use App\Entity\SliderTextTranslation;

final class SliderTextView
{
    public function __construct(
        private readonly string $defaultLocale,
        private readonly array $locales,
    ) {}

    public function editView(SliderText $sliderText): array
    {
        $view = [];
        $view['available_countries'] = $sliderText->getAvailableCountries();

        foreach ($this->locales as $locale) {
            $view['translations'][$locale] = $this->getDescAndLink($sliderText->getByLocale($locale));
        }

        $view['position'] = $sliderText->getPosition();

        return $view;
    }

    public function siteView(SliderText $sliderText, string $locale): array
    {
        $trans = $sliderText->getByLocale($locale);

        if (null === $trans) {
            $trans = $sliderText->getByLocale($this->defaultLocale);
        }

        return $this->getDescAndLink($trans);
    }

    private function getDescAndLink(SliderTextTranslation $sliderTextTranslation): array
    {
        return [
            'title' => $sliderTextTranslation->getTitle(),
            'description' => $sliderTextTranslation->getDescription(),
            'link' => $sliderTextTranslation->getLink(),
        ];
    }
}
