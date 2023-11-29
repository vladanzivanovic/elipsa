<?php

declare(strict_types=1);

namespace App\Parser\Site;

use App\Entity\NewsLetter;
use App\Repository\NewsLetterRepository;
use App\Repository\UserRepository;
use App\Request\Dto\NewsLetterRequestDto;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param NewsLetterRepository $letterRepository
     * @param UserRepository       $userRepository
     * @param TranslatorInterface  $translator
     */
    public function __construct(
        NewsLetterRepository $letterRepository,
        UserRepository $userRepository,
        TranslatorInterface $translator
    ) {
        $this->letterRepository = $letterRepository;
        $this->userRepository = $userRepository;
        $this->translator = $translator;
    }

    /**
     * @param NewsLetterRequestDto $newsLetterRequestDto
     * @return NewsLetter
     */
    public function parse(NewsLetterRequestDto $newsLetterRequestDto): NewsLetter
    {
        $email = $newsLetterRequestDto->email;

        $existing = $this->letterRepository->findOneBy(['email' => $email]);

        if (null !== $existing) {
            throw new BadRequestHttpException($this->translator->trans('newsletter.existingUser', [], null, $newsLetterRequestDto->locale));
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);

        $newsLetter = new NewsLetter();
        $newsLetter->setEmail($email);
        $newsLetter->setUser($user);
        $newsLetter->setGender($newsLetterRequestDto->gender);
        $newsLetter->setLocale($newsLetterRequestDto->locale);

        return $newsLetter;
    }
}
