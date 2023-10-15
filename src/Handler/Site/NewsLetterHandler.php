<?php

declare(strict_types=1);

namespace App\Handler\Site;

use App\Entity\NewsLetter;
use App\Event\NewsLetterEvent;
use App\Helper\ValidatorHelper;
use App\Repository\LoyaltyRepository;
use App\Repository\NewsLetterRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class NewsLetterHandler
{
    private ValidatorHelper $validator;

    private NewsLetterRepository $newsLetterRepository;

    private EventDispatcherInterface $dispatcher;

    private LoyaltyRepository $loyaltyRepository;

    public function __construct(
        ValidatorHelper $validator,
        NewsLetterRepository $newsLetterRepository,
        EventDispatcherInterface $dispatcher,
        LoyaltyRepository $loyaltyRepository
    ) {
        $this->validator = $validator;
        $this->newsLetterRepository = $newsLetterRepository;
        $this->dispatcher = $dispatcher;
        $this->loyaltyRepository = $loyaltyRepository;
    }

    /**
     * @param NewsLetter $newsLetter
     *
     * @return bool
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(NewsLetter $newsLetter): bool
    {
        $errors = $this->validator->validate($newsLetter, null, "SetNewsLetter");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        $this->newsLetterRepository->persist($newsLetter);

        $loyalty = $this->loyaltyRepository->findOneBy(['email' => $newsLetter->getEmail()]);

        $event = new NewsLetterEvent($newsLetter, $loyalty);

        $this->dispatcher->dispatch($event, NewsLetterEvent::ADD_USER);

        return null !== $loyalty;
    }
}
