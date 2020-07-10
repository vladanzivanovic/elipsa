<?php

declare(strict_types=1);

namespace App\Parser\Site;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class ResetPasswordRequestParser
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UserRepository    $userRepository
     */
    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function parse(string $email): User
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (null === $user) {
            throw new BadRequestHttpException('reset_password.user_not_exists');
        }

        $token = bin2hex(openssl_random_pseudo_bytes(10));

        $user->setResetToken($token)
            ->setResetRequestAt(new \DateTime());

        return $user;
    }

    /**
     * @param ParameterBag $bag
     *
     * @return User
     */
    public function parseResetPassword(ParameterBag $bag): User
    {
        $user = $this->userRepository->findOneBy(['resetToken' => $bag->get('token')]);

        if (null === $user) {
            throw new BadRequestHttpException('reset_password.user_not_exists');
        }

        $user->setResetToken(null)
            ->setResetRequestAt(null)
            ->setPassword($bag->get('password'))
            ->setRePassword($bag->get('repeat_password'));

        return $user;
    }
}