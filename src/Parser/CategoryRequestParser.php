<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Category;
use App\Entity\CategoryTranslation;
use App\Entity\ProductColor;
use App\Repository\CategoryRepository;
use App\Repository\CategoryTranslationRepository;
use App\Repository\ProductColorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

final class CategoryRequestParser
{
    use ParserTrait;

    private \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $parameterBag;

    private \App\Repository\CategoryRepository $categoryRepository;
    private \App\Repository\CategoryTranslationRepository $translationRepository;

    /**
     * CategoryRequestParser constructor.
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        CategoryRepository $categoryRepository,
        CategoryTranslationRepository $translationRepository
    ) {
        $this->parameterBag = $parameterBag;
        $this->categoryRepository = $categoryRepository;
        $this->translationRepository = $translationRepository;
    }

    /**
     * @param Category|null $category
     *
     */
    public function parse(ParameterBag $bag, ?Category $category = null): Category
    {
        if (!$category instanceof Category) {
            $category = new Category();
        }

        $category->setParent(null);

        if (!empty($bag->get('parent_category')) && $bag->get('parent_category') !== '-1') {
            $parentTranslation = $this->translationRepository->findOneBy(['slug' => $bag->get('parent_category')]);

            $category->setParent($parentTranslation->getCategory());
        }

        $this->setTranslation($category, $bag);

        return $category;
    }

    private function setTranslation(Category $category, ParameterBag $bag): void
    {

        $locales = $this->setLanguageArray($this->parameterBag, $bag);

        foreach (array_keys($locales) as $locale) {
            $translation = $this->translationRepository->findOneBy(['category' => $category, 'locale' => $locale]);

            if (!$translation instanceof CategoryTranslation) {
                $translation = new CategoryTranslation();
            }

            $translation->setTitle($bag->get($locale.'_title'))
                ->setLocale($locale)
                ->setCategory($category);

            $category->addCategoryTranslation($translation);
        }
    }
}