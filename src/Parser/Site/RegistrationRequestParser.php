<?php

declare(strict_types=1);

namespace App\Parser\Site;

use App\Entity\Address;
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
     * @param User|null    $user
     *
     * @return User
     */
    public function parse(ParameterBag $bag, User $user = null): User
    {
        $countUsers = $this->userRepository->countByEmail($bag->get('registration_email'), $user);

        if ($countUsers > 0) {
            throw new BadRequestHttpException('registration.error.user_exists');
        }

        if (null === $user) {
            $token = bin2hex(openssl_random_pseudo_bytes(10));

            $user = new User();
            $user->setPassword($bag->get('registration_password'))
                ->setRePassword($bag->get('registration_re_password'))
                ->setStatus(User::STATUS_PENDING)
                ->setResetToken($token)
                ->setRoles(['ROLE_USER'])
                ->setResetRequestAt(new \DateTime());
        }

        $user->setFirstName($bag->get('registration_first_name'))
            ->setLastName($bag->get('registration_last_name'))
            ->setEmail($bag->get('registration_email'));

        if (null !== $bag->get('registration_password') && null !== $user->getId()) {
            $user->setPassword($bag->get('registration_password'));
        }

        return $user;
    }

    /**
     * @param ParameterBag $bag
     * @param User         $user
     *
     * @return void
     */
    public function parseAddress(ParameterBag $bag, User $user): void
    {
        $address = $user->getAddress();

        if (null === $address) {
            $address = new Address();
        }

        $address->setEmail($user->getEmail())
            ->setLastName($user->getLastName())
            ->setFirstName($user->getFirstName())
            ->setAddress($bag->get('address'))
            ->setCity($bag->get('city'))
            ->setCountry($bag->get('country'))
            ->setPhone($bag->get('phone'))
            ->setZipCode((int) $bag->get('zipCode'));

        $user->setAddress($address);
    }
}