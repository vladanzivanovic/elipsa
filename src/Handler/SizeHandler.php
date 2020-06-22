<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\ProductSize;
use App\Helper\ValidatorHelper;
use App\Repository\ProductSizeRepository;
use App\Repository\TagsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class SizeHandler
{
    /**
     * @var ProductSizeRepository
     */
    private $sizeRepository;
    /**
     * @var ValidatorHelper
     */
    private $validator;

    /**
     * @param ProductSizeRepository $sizeRepository
     * @param ValidatorHelper       $validator
     */
    public function __construct(
        ProductSizeRepository $sizeRepository,
        ValidatorHelper $validator
    ) {
        $this->sizeRepository = $sizeRepository;
        $this->validator = $validator;
    }

    /**
     * @param ProductSize $productSize
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(ProductSize $productSize): void
    {
        $errors = $this->validator->validate($productSize, null, "SetSize");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        if (is_null($productSize->getId())) {
            $this->sizeRepository->persist($productSize);
        }

        $this->sizeRepository->flush();
    }

    /**
     * @param ProductSize $productSize
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(ProductSize $productSize): void
    {
        $this->sizeRepository->delete($productSize);
        $this->sizeRepository->flush();
    }
}