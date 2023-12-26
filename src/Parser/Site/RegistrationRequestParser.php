<?php

declare(strict_types=1);

namespace App\Parser\Site;

use App\Entity\Address;
use App\Entity\Loyalty;
use App\Entity\User;
use App\Exception\UserException;
use App\Repository\LoyaltyRepository;
use App\Repository\UserRepository;
use App\Request\Dto\AddressRequestDto;
use App\Request\Dto\RegistrationRequestDto;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
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

    public function parse(RegistrationRequestDto $registrationRequestDto, User $user = null): User
    {
        $countUsers = $this->userRepository->countByEmail($registrationRequestDto->email, $user);

        if ($countUsers > 0) {
            $userException = new UserException('registration.error.user_exists');
            $userException->setDomain('messages');

            throw $userException;
        }

        if (null === $user) {
            $token = bin2hex(openssl_random_pseudo_bytes(10));

            $user = $this->create();
            $user->setPassword($registrationRequestDto->password)
                ->setRePassword($registrationRequestDto->rePassword)
                ->setResetToken($token)
                ->setRoles(['ROLE_USER'])
                ->setResetRequestAt(new \DateTime());
        }

        $user->setFirstName($registrationRequestDto->firstName)
            ->setLastName($registrationRequestDto->lastName)
            ->setEmail($registrationRequestDto->email);

//        if (null !== $bag->get('registration_password') && null !== $user->getId()) {
//            $user->setPassword($bag->get('registration_password'));
//        }

        $this->parseAddress($registrationRequestDto->addressRequestDto, $user);

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

    public function parseAddress(AddressRequestDto $addressRequestDto, User $user): void
    {
        $address = $user->getAddress();

        if (null === $address) {
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

    public function create(): User
    {
        return new User();
    }
}
