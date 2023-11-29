<?php

declare(strict_types=1);

namespace App\Request\Dto;

use Nelexa\RequestDtoBundle\Dto\ConstructRequestObjectInterface;
use Symfony\Component\HttpFoundation\Request;

class NewsLetterRequestDto extends AbstractRequestDto
{
    public string $email;

    public string $gender;

    public string $language;

    public function __construct(Request $request = null)
    {
        $body = json_decode($request->getContent(), true);

        $this->gender = $body['newsletter_gender'];
        $this->language = $body['newsletter_language'];
        $this->email = $body['newsletter_email'];

        parent::__construct($request);
    }
}
