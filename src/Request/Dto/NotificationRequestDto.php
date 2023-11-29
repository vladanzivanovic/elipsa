<?php

declare(strict_types=1);

namespace App\Request\Dto;

use Nelexa\RequestDtoBundle\Dto\RequestBodyObjectInterface;

class NotificationRequestDto implements RequestBodyObjectInterface
{
    public array $payload;

    public string $email;

    public string $type;
}
