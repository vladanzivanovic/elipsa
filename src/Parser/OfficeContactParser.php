<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\OfficeContact;
use App\Entity\OfficeContactTranslation;
use Symfony\Component\HttpFoundation\ParameterBag;

final class OfficeContactParser
{
    private array $locales;

    public function __construct(
        string $locales
    ) {
        $this->locales = explode('|', $locales);
    }
    public function parse(ParameterBag $bag, OfficeContact $officeContact = null): OfficeContact
    {
        if (!$officeContact instanceof \App\Entity\OfficeContact) {
            $officeContact = new OfficeContact();
        }

        $officeContact->setTelephone($bag->get('telephone'));
        $officeContact->setShowInFooter($bag->getBoolean('show_in_footer'));
        $officeContact->setUseInEmail($bag->getBoolean('use_in_email'));

        $this->setLocale($bag, $officeContact);

        return $officeContact;
    }

    private function setLocale(ParameterBag $bag, OfficeContact $officeContact): void
    {
        foreach ($this->locales as $locale) {
            $transCollection = $bag->all($locale);

            $trans = new OfficeContactTranslation();

            if (null !== $officeContact->getId()) {
                $trans = $officeContact->getByLocale($locale);
            }

            $trans->setTitle($transCollection['title']);
            $trans->setLocale($locale);

            $officeContact->addOfficeContactTranslation($trans);
        }
    }
}
