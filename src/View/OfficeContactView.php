<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\OfficeContact;
use App\Entity\OfficeContactTranslation;
use App\Entity\SliderText;
use App\Entity\SliderTextTranslation;

final class OfficeContactView
{
    private array $locales;

    public function __construct(
        string $locales
    ) {
        $this->locales = explode('|', $locales);
    }

    public function editView(OfficeContact $officeContact): array
    {
        $view = [];

        foreach ($this->locales as $locale) {
            $view[$locale] = $this->getDescAndLink($officeContact->getByLocale($locale));
        }

        $view['telephone'] = $officeContact->getTelephone();
        $view['isShownInFooter'] = $officeContact->isShownInFooter();

        return $view;
    }

    public function siteView(OfficeContact $officeContact, string $locale): array
    {
        $view = $this->getDescAndLink($officeContact->getByLocale($locale));

        $view['telephone'] = $officeContact->getTelephone();

        return $view;
    }

    private function getDescAndLink(OfficeContactTranslation $officeContactTranslation): array
    {
        return [
            'title' => $officeContactTranslation->getTitle()
        ];
    }
}
