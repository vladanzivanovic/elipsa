<?php

declare(strict_types=1);

namespace App\Parser\Site;

use App\Entity\AskUs;
use App\Request\Dto\AskUsRequestDto;

final class AskUsRequestParser
{
    /**
     * @param AskUsRequestDto $askUsRequestDto
     *
     * @return AskUs
     */
    public function parse(AskUsRequestDto $askUsRequestDto): AskUs
    {
        $askUs = new AskUs();
        $askUs->setFirstName($askUsRequestDto->firstName)
            ->setLastName($askUsRequestDto->lastName)
            ->setEmail($askUsRequestDto->email)
            ->setContactVia($askUsRequestDto->contactVia)
            ->setTelephone($askUsRequestDto->telephone)
            ->setSubject($askUsRequestDto->subject)
            ->setNote($askUsRequestDto->note);

        return $askUs;
    }
}
