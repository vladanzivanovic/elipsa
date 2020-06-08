<?php

declare(strict_types=1);

namespace App\Parser\Site;

use App\Entity\Loyalty;
use App\Entity\User;
use App\Repository\LoyaltyRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class RegistrationRequestParser
{
    /**
     * @var LoyaltyRepository
     */
    private $loyaltyRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param LoyaltyRepository $loyaltyRepository
     * @param UserRepository    $userRepository
     */
    public function __construct(
        LoyaltyRepository $loyaltyRepository,
        UserRepository $userRepository
    ) {
        $this->loyaltyRepository = $loyaltyRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param ParameterBag $bag
     *
     * @return User
     * @throws \Exception
     */
    public function parse(ParameterBag $bag): User
    {
        $countUsers = $this->userRepository->count([
            'email' => $bag->get('registration_email'),
        ]);

        if ($countUsers > 0) {
            throw new BadRequestHttpException('registration.error.user_exists');
        }

        $birthDate = $bag->get('birth_date') !== null ? new \DateTime($bag->get('birth_date')) : null;

        $user = new User();
        $user->setFirstName($bag->get('registration_first_name'))
            ->setLastName($bag->get('registration_last_name'))
            ->setEmail($bag->get('registration_email'))
            ->setPassword($bag->get('registration_password'))
            ->setRePassword($bag->get('registration_re_password'))
            ->setRoles(['ROLE_USER'])
            ->setStatus(User::STATUS_PENDING);

        return $user;
    }
}