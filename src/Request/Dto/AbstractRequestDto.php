<?php

declare(strict_types=1);

namespace App\Request\Dto;

use Nelexa\RequestDtoBundle\Dto\ConstructRequestObjectInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractRequestDto implements ConstructRequestObjectInterface
{
    public ?string $csrf = null;
    public string $locale;
    public string $country;
    public Request $request;

    public function __construct(Request $request, ?string $csrf = null)
    {
        $this->locale = $request->getLocale();
        $this->csrf = $csrf;
        $this->country = $request->attributes->get('_country');
        $this->request = $request;
    }
}
