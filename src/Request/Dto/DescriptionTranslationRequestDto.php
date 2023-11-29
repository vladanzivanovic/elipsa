<?php

declare(strict_types=1);

namespace App\Request\Dto;

class DescriptionTranslationRequestDto
{
    public string $description;

    public string $transLocale;

    public function __construct(array $transBody)
    {
        $this->description = $transBody['description'];

        $this->transLocale = $transBody['locale'];
    }
}
