<?php

declare(strict_types=1);

namespace App\Request\Dto;

use Symfony\Component\HttpFoundation\Request;

final class ResetPasswordSetRequestDto extends AbstractRequestDto
{
    public string $token;

    public string $password;

    public string $repeatPassword;

    public function __construct(Request $request)
    {
        $body = json_decode($request->getContent(), true);

        $this->token = $body['token'];
        $this->password = $body['password'];
        $this->repeatPassword = $body['repeat_password'];

        parent::__construct($request, $body['_csrf_token']);
    }
}
