<?php

declare(strict_types=1);

namespace App\Entity\Resources;

interface LocaleInterface
{
    public function getLocale(): string;

    public function setLocale(string $locale): self;
}
