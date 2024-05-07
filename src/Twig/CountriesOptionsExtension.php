<?php

declare(strict_types=1);

namespace App\Twig;

use App\Formatter\Options\CountryOptionsFormatter;
use App\Formatter\Options\LanguageOptionsFormatter;
use App\Formatter\Options\LocationOptionsFormatter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class CountriesOptionsExtension extends AbstractExtension
{
    public function __construct(
        private readonly CountryOptionsFormatter $countryOptionsFormatter,
        private readonly LanguageOptionsFormatter $languageOptionsFormatter,
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('countries_options', [$this, 'getCountryOptions']),
            new TwigFunction('languages_options', [$this, 'getLangOptions']),
        ];
    }

    public function getCountryOptions(): array
    {
        return $this->countryOptionsFormatter->format();
    }

    public function getLangOptions(string $countryCode = null): array
    {
        return $this->languageOptionsFormatter->format($countryCode);
    }

    public function getName(): string
    {
        return 'location_options_extension';
    }
}
