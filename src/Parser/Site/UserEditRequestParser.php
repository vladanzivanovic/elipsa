<?php

declare(strict_types=1);

namespace App\Parser\Site;

use App\Entity\Address;
use App\Entity\User;
use App\Exception\UserException;
use App\Repository\UserRepository;
use App\Request\Dto\AddressRequestDto;
use App\Request\Dto\UserRequestDto;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserEditRequestParser
{
    private UserRepository $userRepository;

    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasher
    ) {
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws UserException
     */
    public function parse(UserRequestDto $userRequestDto, User $user): User
    {
        $countUsers = $this->userRepository->countByEmail($userRequestDto->email, $user->getId() ? $user : null);

        if ($countUsers > 0) {
            $userException = new UserException('registration.error.user_exists');
            $userException->setDomain('messages');

            throw $userException;
        }

        if (null !== $userRequestDto->password) {
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $userRequestDto->password));
        }

        $user->setFirstName($userRequestDto->firstName)
            ->setLastName($userRequestDto->lastName)
            ->setEmail($userRequestDto->email);

        $this->parseAddress($userRequestDto->addressRequestDto, $user);

        return $user;
    }

    public function parseAddress(AddressRequestDto $addressRequestDto, User $user): void
    {
        $address = $user->getAddress();

        if (!$address instanceof \App\Entity\Address) {
            $address = new Address();
        }

        $address->setEmail($user->getEmail())
            ->setLastName($user->getLastName())
            ->setFirstName($user->getFirstName())
            ->setAddress($addressRequestDto->street)
            ->setCity($addressRequestDto->city)
            ->setCountry($addressRequestDto->country)
            ->setPhone($addressRequestDto->mobilePhone)
            ->setZipCode($addressRequestDto->zipCode);

        $user->setAddress($address);
    }
}
