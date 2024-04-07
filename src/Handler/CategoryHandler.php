<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Category;
use App\Entity\ProductHasCategories;
use App\Helper\ValidatorHelper;
use App\Repository\CategoryRepository;
use App\Repository\ProductColorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class CategoryHandler
{
    private \App\Repository\CategoryRepository $categoryRepository;
    private \App\Helper\ValidatorHelper $validator;

    /**
     * CategoryHandler constructor.
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        ValidatorHelper $validator
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->validator = $validator;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Category $category, bool $isEdit = false): void
    {
        $errors = $this->validator->validate($category, null, "SetColor");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        if (false === $isEdit) {
            $this->categoryRepository->persist($category);
        }

        $this->categoryRepository->flush();
    }

    /**
     * @throws ORMException
     */
    public function remove(Category $category): void
    {
        $this->categoryRepository->delete($category);

        $this->categoryRepository->flush();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function toggleHomePage(Category $category, bool $status): void
    {
        $category->setShowHomePage($status);

        $this->categoryRepository->flush();
    }
}