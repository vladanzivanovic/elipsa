<?php

declare(strict_types=1);

namespace App\Request\Dto;

use Symfony\Component\HttpFoundation\Request;

final class ResetPasswordRequestDto extends AbstractRequestDto
{
    public string $resetEmail;

    public function __construct(Request $request)
    {
        $body = json_decode($request->getContent(), true);

        $this->resetEmail = $body['reset_email'];

        parent::__construct($request, $body['_csrf_token']);
    }
}
