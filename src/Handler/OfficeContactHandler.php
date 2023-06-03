<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\OfficeContact;
use App\Helper\ValidatorHelper;
use App\Repository\OfficeContactRepository;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class OfficeContactHandler
{
    private ValidatorHelper $validator;

    private OfficeContactRepository $repository;

    public function __construct(
        ValidatorHelper $validator,
        OfficeContactRepository $repository
    ) {
        $this->validator = $validator;
        $this->repository = $repository;
    }

    /**
     * @throws \Exception
     */
    public function save(OfficeContact $officeContact): void
    {
        $errors = $this->validator->validate($officeContact, null, "SetSliderText");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        if (null === $officeContact->getId()) {
            $this->repository->persist($officeContact);
        }

        $this->repository->flush();
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(OfficeContact $officeContact): void
    {
        $this->repository->delete($officeContact);

        $this->repository->flush();
    }
}
