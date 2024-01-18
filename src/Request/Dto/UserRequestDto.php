<?php

declare(strict_types=1);

namespace App\Request\Dto;

use Symfony\Component\HttpFoundation\Request;

class UserRequestDto extends AbstractRequestDto
{
    public string $firstName;

    public string $lastName;

    public string $email;

    public ?string $password = null;

    public AddressRequestDto $addressRequestDto;

    public function __construct(Request $request)
    {
        $body = json_decode($request->getContent(), true);

        $this->firstName = $body['first_name'];
        $this->lastName = $body['last_name'];
        $this->email = $body['email'];
        $this->password = $body['password'] ?? null;

        $this->addressRequestDto = new AddressRequestDto($body['address']);

        parent::__construct($request, $body['_csrf_token']);
    }
}
