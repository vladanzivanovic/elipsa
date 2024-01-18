<?php

declare(strict_types=1);

namespace App\Request\Dto;

use Symfony\Component\HttpFoundation\Request;

final class RegistrationRequestDto extends AbstractRequestDto
{
    public UserRequestDto $userRequestDto;

    public string $rePassword;

    public function __construct(Request $request)
    {
        $body = json_decode($request->getContent(), true);

        $this->userRequestDto = new UserRequestDto($request);
        $this->rePassword = $body['re_password'];

        parent::__construct($request, $body['_csrf_token']);
    }
}
