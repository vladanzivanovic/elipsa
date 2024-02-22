<?php

declare(strict_types=1);

namespace App\Request\Dto;

use Nelexa\RequestDtoBundle\Dto\ConstructRequestObjectInterface;
use Symfony\Component\HttpFoundation\Request;

class OrderRequestDto extends AbstractRequestDto
{
    public ?string $token = null;

    public function __construct(Request $request)
    {
        $this->token = $request->attributes->get('token');

        parent::__construct($request);
    }
}
