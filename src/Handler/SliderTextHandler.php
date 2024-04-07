<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\SliderText;
use App\Helper\ValidatorHelper;
use App\Repository\SliderTextRepository;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class SliderTextHandler
{
    private ValidatorHelper $validator;

    private SliderTextRepository $repository;

    public function __construct(
        ValidatorHelper $validator,
        SliderTextRepository $repository
    ) {
        $this->validator = $validator;
        $this->repository = $repository;
    }

    /**
     *
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(SliderText $sliderText): void
    {
        $errors = $this->validator->validate($sliderText, null, "SetSliderText");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        if (null === $sliderText->getId()) {
            $this->repository->persist($sliderText);
        }

        $this->repository->flush();
    }

    /**
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(SliderText $sliderText): void
    {
        $this->repository->delete($sliderText);

        $this->repository->flush();
    }
}
