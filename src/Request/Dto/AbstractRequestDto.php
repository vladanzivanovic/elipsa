<?php

declare(strict_types=1);

namespace App\Request\Dto;

use Nelexa\RequestDtoBundle\Dto\ConstructRequestObjectInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractRequestDto implements ConstructRequestObjectInterface
{
    public string $locale;

    public function __construct(Request $request)
    {
        $this->locale = $request->getLocale();
    }
}
