<?php

declare(strict_types=1);

namespace App\Factory;

final class NumberFormatterFactory
{
    public function create(string $locale, int $style): \NumberFormatter
    {
        return new \NumberFormatter($locale, $style);
    }
}
