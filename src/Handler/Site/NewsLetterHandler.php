<?php

declare(strict_types=1);

namespace App\Handler\Site;

use App\Entity\NewsLetter;
use App\Entity\User;
use App\Event\NewsLetterEvent;
use App\Helper\ValidatorHelper;
use App\Repository\NewsLetterRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class NewsLetterHandler
{
    /**
     * @var ValidatorHelper
     */
    private $validator;
    /**
     * @var NewsLetterRepository
     */
    private $newsLetterRepository;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * NewsLetterHandler constructor.
     *
     * @param ValidatorHelper          $validator
     * @param NewsLetterRepository     $newsLetterRepository
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        ValidatorHelper $validator,
        NewsLetterRepository $newsLetterRepository,
        EventDispatcherInterface $dispatcher
    ) {
        $this->validator = $validator;
        $this->newsLetterRepository = $newsLetterRepository;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param NewsLetter $newsLetter
     *
     * @return void
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(NewsLetter $newsLetter): void
    {
        $errors = $this->validator->validate($newsLetter, null, "SetNewsLetter");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        $this->newsLetterRepository->persist($newsLetter);
        $this->newsLetterRepository->flush();

        $event = new NewsLetterEvent($newsLetter);

        $this->dispatcher->dispatch($event, NewsLetterEvent::ADD_USER);
    }
}