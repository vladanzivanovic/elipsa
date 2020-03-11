<?php

declare(strict_types=1);

namespace App\Handler;

use App\Helper\ValidatorHelper;
use App\Repository\ProductColorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class ProductColorHandler
{
    /**
     * @var ProductColorRepository
     */
    private $colorRepository;
    /**
     * @var ValidatorHelper
     */
    private $validator;

    /**
     * ProductColorHandler constructor.
     *
     * @param ProductColorRepository $colorRepository
     * @param ValidatorHelper        $validator
     */
    public function __construct(
        ProductColorRepository $colorRepository,
        ValidatorHelper $validator
    ) {
        $this->colorRepository = $colorRepository;
        $this->validator = $validator;
    }

    /**
     * @param ArrayCollection $colors
     * @param bool            $isEdit
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(ArrayCollection $colors, bool $isEdit = false): void
    {
        $errors = $this->validator->validate($colors, null, "SetColor");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        if (false === $isEdit) {
            foreach ($colors as $color) {
                $this->colorRepository->persist($color);
            }
        }

        $this->colorRepository->flush();
    }

    /**
     * @param string $mainSLug
     * @return void
     */
    public function remove(string $mainSLug): void
    {
        $this->colorRepository->remove($mainSLug);
    }
}