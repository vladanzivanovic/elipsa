<?php

declare(strict_types=1);

namespace App\Parser\Site;

use App\Entity\NewsLetter;
use App\Entity\User;
use App\Repository\NewsLetterRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class NewsLetterRequestParser
{
    /**
     * @var NewsLetterRepository
     */
    private $letterRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param NewsLetterRepository $letterRepository
     * @param UserRepository       $userRepository
     */
    public function __construct(
        NewsLetterRepository $letterRepository,
        UserRepository $userRepository
    ) {
        $this->letterRepository = $letterRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param ParameterBag $bag
     *
     * @return NewsLetter
     */
    public function parse(ParameterBag $bag): NewsLetter
    {
        $email = $bag->get('email');

        $existing = $this->letterRepository->findOneBy(['email' => $email]);
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (null !== $existing) {
            throw new BadRequestHttpException('newsletter.existingUser');
        }

        $newsLetter = new NewsLetter();
        $newsLetter->setEmail($email)
            ->setUser($user);

        return $newsLetter;
    }
}