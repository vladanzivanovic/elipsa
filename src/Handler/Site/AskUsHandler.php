<?php

declare(strict_types=1);

namespace App\Handler\Site;

use App\Entity\AskUs;
use App\Helper\ValidatorHelper;
use App\Repository\AskUsRepository;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class AskUsHandler
{
    private ValidatorHelper $validator;

    private AskUsRepository $askUsRepository;

    public function __construct(
        ValidatorHelper $validator,
        AskUsRepository $askUsRepository
    ) {
        $this->validator = $validator;
        $this->askUsRepository = $askUsRepository;
    }

    /**
     * @param AskUs $askUs
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public function save(AskUs $askUs): void
    {
        $errors = $this->validator->validate($askUs, null, "SetAskUs");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        $this->askUsRepository->persist($askUs);
        $this->askUsRepository->flush();
    }
}
