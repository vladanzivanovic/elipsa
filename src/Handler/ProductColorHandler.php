<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\ProductColor;
use App\Helper\ValidatorHelper;
use App\Repository\ProductColorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class ProductColorHandler
{
    private \App\Repository\ProductColorRepository $colorRepository;
    private \App\Helper\ValidatorHelper $validator;

    /**
     * ProductColorHandler constructor.
     */
    public function __construct(
        ProductColorRepository $colorRepository,
        ValidatorHelper $validator
    ) {
        $this->colorRepository = $colorRepository;
        $this->validator = $validator;
    }

    /**
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(ProductColor $productColor): void
    {
        $errors = $this->validator->validate($productColor, null, "SetColor");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        if (null === $productColor->getId()) {
            $this->colorRepository->persist($productColor);
        }

        $this->colorRepository->flush();
    }

    /**
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(ProductColor $productColor): void
    {
        $this->colorRepository->delete($productColor);

        $this->colorRepository->flush();
    }
}