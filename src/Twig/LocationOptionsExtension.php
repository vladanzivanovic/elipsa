<?php

declare(strict_types=1);

namespace App\Twig;

use App\Formatter\Options\LocationOptionsFormatter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class LocationOptionsExtension extends AbstractExtension
{
    public function __construct(
        private readonly LocationOptionsFormatter $locationOptionsFormatter
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('location_options', [$this, 'getOptions']),
        ];
    }

    public function getOptions(string $locale): array
    {
        return $this->locationOptionsFormatter->format($locale);
    }

    public function getName(): string
    {
        return 'location_options_extension';
    }
}
