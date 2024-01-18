<?php

declare(strict_types=1);

namespace App\Parser\Site;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Request\Dto\ResetPasswordRequestDto;
use App\Request\Dto\ResetPasswordSetRequestDto;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class ResetPasswordRequestParser
{
    private UserRepository $userRepository;

    private UserPasswordHasherInterface $userPasswordHasher;

    /**
     * @param UserRepository    $userRepository
     */
    public function __construct(
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasher
    ) {
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function parse(ResetPasswordRequestDto $resetPasswordRequestDto): User
    {
        $user = $this->userRepository->findOneBy(['email' => $resetPasswordRequestDto->resetEmail]);

        if (null === $user) {
            throw new BadRequestHttpException('reset_password.user_not_exists');
        }

        $token = bin2hex(openssl_random_pseudo_bytes(10));

        $user->setResetToken($token)
            ->setResetRequestAt(new \DateTime());

        return $user;
    }

    /**
     *
     * @param ResetPasswordSetRequestDto $resetPasswordSetRequestDto
     * @return User
     */
    public function parseResetPassword(ResetPasswordSetRequestDto $resetPasswordSetRequestDto): User
    {
        $user = $this->userRepository->findOneBy(['resetToken' => $resetPasswordSetRequestDto->token]);

        if (null === $user) {
            throw new BadRequestHttpException('reset_password.user_not_exists');
        }

        $user->setResetToken(null)
            ->setResetRequestAt(null)
            ->setPlainPassword($resetPasswordSetRequestDto->password)
            ->setPassword($this->userPasswordHasher->hashPassword($user, $resetPasswordSetRequestDto->password))
            ->setRePassword($resetPasswordSetRequestDto->repeatPassword);

        return $user;
    }
}
