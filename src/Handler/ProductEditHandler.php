<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Product;
use App\Helper\ValidatorHelper;
use App\Repository\ProductRepository;
use App\Services\ProductImageService;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class ProductEditHandler
{
    /**
     * @var ValidatorHelper
     */
    private $validator;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param ValidatorHelper   $validator
     * @param ProductRepository $productRepository
     */
    public function __construct(
        ValidatorHelper $validator,
        ProductRepository $productRepository
    ) {
        $this->validator = $validator;
        $this->productRepository = $productRepository;
    }

    /**
     * @param Product $product
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @return void
     */
    public function save(Product $product): void
    {

        $errors = $this->validator->validate($product, null, "SetProduct");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        if (is_null($product->getId())) {
            $this->productRepository->persist($product);
        }

        $this->productRepository->flush();
    }

    /**
     * @param Product $product
     * @param int     $status
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function changeStatus(Product $product, int $status): void
    {
        $product->setStatus($status);

        $this->productRepository->flush();
    }

    /**
     * @param Product $product
     * @param bool    $status
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function toggleHomePage(Product $product, bool $status): void
    {
        $product->setShowHomePage($status);

        $this->productRepository->flush();
    }
}