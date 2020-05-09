<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class DotenvExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('getenv', [$this, 'getEnvValue']),
        ];
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getEnvValue(string $name): string
    {
        return $_ENV[$name];
    }
}