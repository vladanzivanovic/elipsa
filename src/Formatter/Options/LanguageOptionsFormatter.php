<?php

declare(strict_types=1);

namespace App\Formatter\Options;

use Symfony\Contracts\Translation\TranslatorInterface;

final class LanguageOptionsFormatter
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly array $countries,
        private readonly string $defaultLocale,
        private readonly array $languages,
    ) {}

    public function format(string $countryCode = null): array
    {
        $options = [];

        $countryCode = $countryCode ?? $this->defaultLocale;

        foreach ($this->countries[$countryCode]['languages'] as $localeCode) {
            $language = $this->languages[$localeCode];

            $options[] = [
                'title' => $this->translator->trans($language['translation']),
                'value' => $localeCode,
            ];
        }

        return $options;
    }
}
