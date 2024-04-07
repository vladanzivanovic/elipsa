<?php

declare(strict_types=1);

namespace App\Parser\Site\Order;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class OrderUserParser
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

    public function parse(ParameterBag $bag)
    {
        $user = $this->userRepository->findOneBy(['email' => $bag->get('email')]);

        if (null === $user) {
            $user = $this->create();
            $user->setEmail($bag->get('email'));
        }

        $user->setFirstName($bag->get('first_name'));
        $user->setLastName($bag->get('last_name'));

        if ($bag->getBoolean('create_account')) {
            $encodedPwd = $this->userPasswordHasher->hashPassword($user, $bag->get('password'));
            $user->setPassword($encodedPwd);
            $user->setResetToken(bin2hex(openssl_random_pseudo_bytes(10)));
            $user->setResetRequestAt(new \DateTimeImmutable());
            $user->setRoles(['ROLE_USER']);
        }

        return $user;
    }

    private function create(): User
    {
        $user = new User();
        $user->setStatus(User::STATUS_PENDING);

        return $user;
    }
}
