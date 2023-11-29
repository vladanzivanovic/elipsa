<?php

declare(strict_types=1);

namespace App\Formatter\Options;

use Symfony\Contracts\Translation\TranslatorInterface;

final class CountryOptionsFormatter
{
    private TranslatorInterface $translator;

    private array $countries;

    public function __construct(
        TranslatorInterface $translator,
        array $countries
    ) {
        $this->countries = $countries;
        $this->translator = $translator;
    }

    public function format(): array
    {
        $options = [];

        foreach ($this->countries as $country) {
            $options[] = [
                'title' => $this->translator->trans($country['name']),
                'value' => $country['code'],
            ];
        }

        return $options;
    }
}
