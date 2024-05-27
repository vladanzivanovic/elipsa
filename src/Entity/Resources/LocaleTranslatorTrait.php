<?php

declare(strict_types=1);

namespace App\Entity\Resources;

use App\Entity\SliderTranslation;

trait LocaleTranslatorTrait
{
    public function getByLocale(string $locale): null|LocaleInterface
    {
        $transCollection = $this->translations;

        $filtered = $transCollection->filter(function ($trans) use ($locale) {
            /** @var LocaleInterface $trans */
            return $trans->getLocale() === $locale;
        });

        return 0 < $filtered->count() ? $filtered->first() : null;
    }
}
