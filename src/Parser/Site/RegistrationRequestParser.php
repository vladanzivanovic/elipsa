<?php

declare(strict_types=1);

namespace App\Parser\Site;

use App\Entity\Address;
use App\Entity\User;
use App\Exception\UserException;
use App\Repository\UserRepository;
use App\Request\Dto\AddressRequestDto;
use App\Request\Dto\RegistrationRequestDto;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;

final class RegistrationRequestParser
{
    private UserRepository $userRepository;

    private UserEditRequestParser $editRequestParser;

    public function __construct(
        UserRepository $userRepository,
        UserEditRequestParser $editRequestParser
    ) {
        $this->userRepository = $userRepository;
        $this->editRequestParser = $editRequestParser;
    }

    public function parse(RegistrationRequestDto $registrationRequestDto, User $user = null): User
    {
        if (!$user instanceof \App\Entity\User) {
            $token = bin2hex(openssl_random_pseudo_bytes(10));

            $user = $this->create();
            $user->setPlainPassword($registrationRequestDto->userRequestDto->password)
                ->setRePassword($registrationRequestDto->rePassword)
                ->setResetToken($token)
                ->setRoles(['ROLE_USER'])
                ->setResetRequestAt(new \DateTime());
        }

        $this->editRequestParser->parse($registrationRequestDto->userRequestDto, $user);

        return $user;
    }

    public function parseFromSocial(UserResponseInterface $userResponse): User
    {
        $user = $this->userRepository->findOneBy(['email' => $userResponse->getEmail()]);

        if (null === $user) {
            $user = $this->create();

            $user->setFirstName($userResponse->getFirstName())
                ->setLastName($userResponse->getLastName())
                ->setEmail($userResponse->getEmail())
                ->setRoles(['ROLE_USER'])
                ->setStatus(User::STATUS_ACTIVE);
        }

        $user->setLoginType($userResponse->getResourceOwner()->getName())
            ->setSocialId($userResponse->getUserIdentifier());

        return $user;
    }

    public function create(): User
    {
        return new User();
    }
}
