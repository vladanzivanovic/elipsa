<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Address;
use App\Entity\Loyalty;
use App\Entity\User;
use App\Repository\LoyaltyRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class UserEditRequestParser
{
    private \App\Repository\UserRepository $userRepository;

    public function __construct(
        LoyaltyRepository $loyaltyRepository,
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     *
     * @param User|null    $user
     *
     */
    public function parse(ParameterBag $bag, User $user = null): User
    {
        $countUsers = $this->userRepository->countByEmail($bag->get('email'), $user);

        if ($countUsers > 0) {
            throw new BadRequestHttpException('registration.error.user_exists');
        }

        if (!$user instanceof \App\Entity\User) {
            $user = new User();
            $user->setPassword($bag->get('password'))
                ->setStatus(User::STATUS_PENDING);
        }

        $user->setFirstName($bag->get('first_name'))
            ->setLastName($bag->get('last_name'))
            ->setRoles([$bag->get('role')])
            ->setEmail($bag->get('email'));

        if (null !== $bag->get('password') && null !== $user->getId()) {
            $user->setPassword($bag->get('password'));
        }

        return $user;
    }

    
    public function parseAddress(ParameterBag $bag, User $user): void
    {
        $address = $user->getAddress();

        if (!$address instanceof \App\Entity\Address) {
            $address = new Address();
        }

        $address->setEmail($user->getEmail())
            ->setLastName($user->getLastName())
            ->setFirstName($user->getFirstName())
            ->setAddress($bag->get('address'))
            ->setCity($bag->get('city'))
            ->setCountry($bag->get('country'))
            ->setPhone($bag->get('phone'))
            ->setZipCode((int) $bag->get('zip_code'));

        $user->setAddress($address);
    }
}