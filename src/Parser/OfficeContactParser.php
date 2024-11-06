<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\OfficeContact;
use App\Entity\OfficeContactTranslation;
use App\Request\Dto\Admin\OfficeContactEditRequestDto;
use Symfony\Component\HttpFoundation\ParameterBag;

final class OfficeContactParser
{
    public function __construct(
        private readonly array $locales,
    ) {}

    public function parse(OfficeContactEditRequestDto $officeContactEditRequestDto, OfficeContact $officeContact = null): OfficeContact
    {
        if (!$officeContact instanceof OfficeContact) {
            $officeContact = new OfficeContact();
        }

        $officeContact->setTelephone($officeContactEditRequestDto->telephone);
        $officeContact->setShowInFooter($officeContactEditRequestDto->showInFooter);
        $officeContact->setUseInEmail($officeContactEditRequestDto->useInEmail);
        $officeContact->setAvailableCountries($officeContactEditRequestDto->availableCountries);

        $this->setLocale($officeContactEditRequestDto, $officeContact);

        return $officeContact;
    }

    private function setLocale(OfficeContactEditRequestDto $officeContactEditRequestDto, OfficeContact $officeContact): void
    {
        foreach ($this->locales as $locale) {
            $transCollection = $officeContactEditRequestDto->translations[$locale] ?? null;

            if (null === $transCollection) {
                continue;
            }

            $trans = $officeContact->getByLocale($locale);

            if (null === $trans) {
                $trans = new OfficeContactTranslation();
            }

            $trans->setTitle($transCollection['title']);
            $trans->setLocale($locale);

            $officeContact->addOfficeContactTranslation($trans);
        }
    }
}
