<?php

declare(strict_types=1);

namespace App\Request\Dto;

use Symfony\Component\HttpFoundation\Request;

final class AskUsRequestDto extends AbstractRequestDto
{
    public string $email;

    public string $firstName;

    public string $lastName;

    public ?string $telephone = null;

    public string $contactVia;

    public string $note;

    public string $subject;

    public function __construct(Request $request = null)
    {
        $body = json_decode($request->getContent(), true);

        $this->email = $body['email'];
        $this->firstName = $body['first_name'];
        $this->lastName = $body['last_name'];
        $this->telephone = $body['telephone'] ?? null;
        $this->contactVia = $body['contact_via'];
        $this->note = $body['note'];
        $this->subject = $body['subject'];

        parent::__construct($request, $body['_csrf_token']);
    }
}
