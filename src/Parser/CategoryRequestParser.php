<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Category;
use App\Entity\CategoryTranslation;
use App\Repository\CategoryTranslationRepository;
use App\Request\Dto\Admin\CategoryEditRequestDto;

final class CategoryRequestParser
{
    public function __construct(
        private readonly CategoryTranslationRepository $translationRepository,
        private readonly array $locales,
    ) {}

    public function parse(CategoryEditRequestDto $categoryEditRequestDto, Category $category = null): Category
    {
        if (!$category instanceof Category) {
            $category = new Category();
        }

        $category->setParent(null);

        if (null !== $categoryEditRequestDto->parent) {
            $parentTranslation = $this->translationRepository->findOneBy(['slug' => $categoryEditRequestDto->parent]);

            $category->setParent($parentTranslation->getCategory());
        }

        $this->setTranslation($category, $categoryEditRequestDto);

        return $category;
    }

    private function setTranslation(Category $category, CategoryEditRequestDto $categoryEditRequestDto): void
    {
        foreach ($this->locales as $locale) {
            $transCollection = $categoryEditRequestDto->translations[$locale];

            $trans = $this->translationRepository->findOneBy(['category' => $category, 'locale' => $locale]);

            if (null === $trans) {
                $trans = new CategoryTranslation();
                $trans->setLocale($locale);
            }

            $trans->setTitle($transCollection['title']);

            $category->addCategoryTranslation($trans);
        }
    }
}
