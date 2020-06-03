<?php

declare(strict_types=1);

namespace App\Parser\Site;

use App\Entity\Loyalty;
use App\Repository\LoyaltyRepository;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class LoyaltyRequestParser
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
     * @return Loyalty
     * @throws \Exception
     */
    public function parse(ParameterBag $bag): Loyalty
    {
        $countLoyaltyByUser = $this->loyaltyRepository->count([
            'firstName' => $bag->get('first_name'),
            'lastName' => $bag->get('last_name'),
            'email' => $bag->get('email'),
        ]);

        if ($countLoyaltyByUser > 0) {
            throw new BadRequestHttpException('loyalty.message.user_exists');
        }

        $birthDate = $bag->get('birth_date') !== null ? new \DateTime($bag->get('birth_date')) : null;

        $loyalty = new Loyalty();
        $loyalty->setFirstName($bag->get('first_name'))
            ->setLastName($bag->get('last_name'))
            ->setEmail($bag->get('email'))
            ->setBirthDate($birthDate)
            ->setMobilePhone($bag->get('mobile_phone'))
            ->setNote($bag->get('note'))
            ->setOccupation($bag->get('occupation'))
            ->setRate((int) $bag->get('rate'));

        return $loyalty;
    }
}