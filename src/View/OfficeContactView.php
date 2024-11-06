<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\OfficeContact;
use App\Entity\OfficeContactTranslation;

final class OfficeContactView
{
    public function __construct(
        private readonly array $locales,
    ) {}

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
                continue;
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
