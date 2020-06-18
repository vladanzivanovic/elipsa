<?php

declare(strict_types=1);

namespace App\Parser\Site;

use App\Entity\AskUs;
use App\Entity\Loyalty;
use App\Repository\LoyaltyRepository;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class AskUsRequestParser
{
    /**
     * @var LoyaltyRepository
     */
    private $loyaltyRepository;

    /**
     * @param LoyaltyRepository $loyaltyRepository
     */
    public function __construct(
        LoyaltyRepository $loyaltyRepository
    ) {
        $this->loyaltyRepository = $loyaltyRepository;
    }

    /**
     * @param ParameterBag $bag
     *
     * @return AskUs
     * @throws \Exception
     */
    public function parse(ParameterBag $bag): AskUs
    {
        $askUs = new AskUs();
        $askUs->setFirstName($bag->get('first_name'))
            ->setLastName($bag->get('last_name'))
            ->setEmail($bag->get('email'))
            ->setSubject($bag->get('subject'));

        return $askUs;
    }
}