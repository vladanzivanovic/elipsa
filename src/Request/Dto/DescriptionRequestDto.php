<?php

declare(strict_types=1);

namespace App\Request\Dto;

use Symfony\Component\HttpFoundation\Request;

final class DescriptionRequestDto extends AbstractRequestDto
{
    public string $type;

    public array $translations = [];

    public function __construct(Request $request = null)
    {
        $body = json_decode($request->getContent(), true);

        $this->type = $body['type'];

        foreach ($body['translations'] as $locale => $translation) {
            $translation['locale'] = $locale;
            $this->translations[$locale] = new DescriptionTranslationRequestDto($translation);
        }

        parent::__construct($request);
    }
}
