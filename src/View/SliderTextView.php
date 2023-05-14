<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\SliderText;
use App\Entity\SliderTextTranslation;

final class SliderTextView
{
    private array $locales;

    public function __construct(
        string $locales
    ) {
        $this->locales = explode('|', $locales);
    }

    public function editView(SliderText $sliderText): array
    {
        $view = [];

        foreach ($this->locales as $locale) {
            $view[$locale] = $this->getDescAndLink($sliderText->getByLocale($locale));
        }

        $view['position'] = $sliderText->getPosition();

        return $view;
    }

    public function siteView(SliderText $sliderText, string $locale): array
    {
        return $this->getDescAndLink($sliderText->getByLocale($locale));
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
