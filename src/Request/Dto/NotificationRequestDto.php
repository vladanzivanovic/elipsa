<?php

declare(strict_types=1);

namespace App\Request\Dto;

use Symfony\Component\HttpFoundation\Request;

class NotificationRequestDto extends AbstractRequestDto
{
    public array $payload;

    public string $email;

    public string $type;

    public function __construct(Request $request)
    {
        $body = json_decode($request->getContent(), true);

        $this->payload = $body['payload'];
        $this->type = $body['type'];
        $this->email = $body['email'];

        parent::__construct($request);
    }
}
