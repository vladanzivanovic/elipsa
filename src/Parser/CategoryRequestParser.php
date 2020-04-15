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

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var CategoryTranslationRepository
     */
    private $translationRepository;

    /**
     * CategoryRequestParser constructor.
     *
     * @param ParameterBagInterface         $parameterBag
     * @param CategoryRepository            $categoryRepository
     * @param CategoryTranslationRepository $translationRepository
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
     * @param ParameterBag  $bag
     * @param Category|null $category
     *
     * @return Category
     */
    public function parse(ParameterBag $bag, ?Category $category = null): Category
    {
        if (!$category instanceof Category) {
            $category = new Category();
        }

        $category->setShowHomePage((boolean) $bag->get('show_home_page'));

        if (!empty($bag->get('parent_category')) && $bag->get('parent_category') !== 'Izaberite...') {
            $parentTranslation = $this->translationRepository->findOneBy(['slug' => $bag->get('parent_category')]);

            $category->setParent($parentTranslation->getCategory());
        }

        $this->setTranslation($category, $bag);

        return $category;
    }

    private function setTranslation(Category $category, ParameterBag $bag)
    {

        $locales = $this->setLanguageArray($this->parameterBag, $bag);

        foreach ($locales as $locale => $langBag) {
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