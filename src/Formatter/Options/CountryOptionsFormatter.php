<?php

declare(strict_types=1);

namespace App\Formatter\Options;

use Symfony\Contracts\Translation\TranslatorInterface;

final class CountryOptionsFormatter
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly array $countries,
    ) {}

    public function format(): array
    {
        $options = [];

        foreach ($this->countries as $localeCode => $country) {
            $options[] = [
                'title' => $this->translator->trans($country['translation']),
                'value' => $localeCode,
            ];
        }

        return $options;
    }
}
