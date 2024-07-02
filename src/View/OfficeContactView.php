<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\OfficeContact;
use App\Entity\OfficeContactTranslation;
use App\Entity\SliderText;
use App\Entity\SliderTextTranslation;

final class OfficeContactView
{
    public function __construct(
        private readonly string $defaultLocale,
        private readonly array $locales,
    ) {}

    public function editView(OfficeContact $officeContact): array
    {
        $view = [
            'translations' => $this->getTranslations($officeContact),
            'telephone' => $officeContact->getTelephone(),
            'show_in_footer' => $officeContact->isShownInFooter(),
            'use_in_email' => $officeContact->isUseInEmail(),
        ];

        $view['telephone'] = $officeContact->getTelephone();
        $view['isShownInFooter'] = $officeContact->isShownInFooter();

        return $view;
    }

    public function siteView(OfficeContact $officeContact): array
    {
        return [
            'translations' => $this->getTranslations($officeContact),
            'telephone' => $officeContact->getTelephone(),
            'show_in_footer' => $officeContact->isShownInFooter(),
            'use_in_email' => $officeContact->isUseInEmail(),
            'available_countries' => $officeContact->getAvailableCountries(),
        ];
    }

    private function getTranslations(OfficeContact $officeContact): array
    {
        $translations = [];

        foreach ($this->locales as $locale) {
            $trans = $officeContact->getByLocale($locale);

            if(null === $trans) {
                $trans = $officeContact->getByLocale($this->defaultLocale);
            }

            $translations[$locale] = $this->getDescAndLink($trans);
        }

        return $translations;
    }

    private function getDescAndLink(OfficeContactTranslation $officeContactTranslation): array
    {
        return [
            'title' => $officeContactTranslation->getTitle()
        ];
    }
}
