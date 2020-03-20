<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Product;
use App\Entity\ProductHasCategories;
use App\Entity\ProductHasSizes;
use App\Entity\ProductHasTags;
use App\Entity\ProductTranslation;
use App\Repository\CategoryTranslationRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\ProductTranslationRepository;
use App\Services\ProductImageService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

final class ProductEditRequestParser
{
    use ParserTrait;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * @var ProductTranslationRepository
     */
    private $translationRepository;

    /**
     * @var CategoryTranslationRepository
     */
    private $categoryTranslationRepository;
    /**
     * @var ProductSizeRepository
     */
    private $sizeRepository;
    /**
     * @var ProductImageService
     */
    private $imageService;

    /**
     * ProductEditRequestParser constructor.
     *
     * @param ParameterBagInterface         $parameterBag
     * @param ProductTranslationRepository  $translationRepository
     * @param CategoryTranslationRepository $categoryTranslationRepository
     * @param ProductSizeRepository         $sizeRepository
     * @param ProductImageService           $imageService
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        ProductTranslationRepository $translationRepository,
        CategoryTranslationRepository $categoryTranslationRepository,
        ProductSizeRepository $sizeRepository,
        ProductImageService $imageService
    ) {
        $this->parameterBag = $parameterBag;
        $this->translationRepository = $translationRepository;
        $this->categoryTranslationRepository = $categoryTranslationRepository;
        $this->sizeRepository = $sizeRepository;
        $this->imageService = $imageService;
    }

    /**
     * @param ParameterBag $bag
     * @param Product|null $product
     *
     * @return Product
     * @throws \Doctrine\ORM\ORMException
     */
    public function parse(ParameterBag $bag, ?Product $product = null): Product
    {
        if (!$product instanceof Product) {
            $product = new Product();
            $product->setStatus(Product::STATUS_PENDING);
        }

        $product->setCode($bag->get('code'));
        $product->setDiscount($bag->getInt('discount'));
        $product->setPrice($bag->getInt('price'));

        $this->setLocales($bag, $product);
        $this->setCategories($product, $bag->get('categories'));
        $this->setTags($product, $bag->get('tags'));
        $this->setSizes($product, $bag->get('sizes'));
        $this->imageService->setImages($product->getProductTranslations()->first(), json_decode($bag->get('images'), true));

        return $product;
    }

    /**
     * @param ParameterBag $bag
     * @param Product      $product
     */
    private function setLocales(ParameterBag $bag, Product $product): void
    {
        $locales = $this->setLanguageArray($this->parameterBag, $bag);

        foreach ($locales as $locale => $langBag) {
            $trans = new ProductTranslation();

            if (!is_null($product->getId())) {
                $trans = $this->translationRepository->findOneBy(['product' => $product, 'locale' => $locale]);
            }

            $trans->setTitle($bag->get($locale.'_title'));
            $trans->setDescription($bag->get($locale.'_description'));
            $trans->setShortDescription($bag->get($locale.'_short_description'));
            $trans->setLocale($locale);

            $product->addProductTranslation($trans);
        }
    }

    /**
     * @param Product $product
     * @param array   $categories
     */
    private function setCategories(Product $product, array $categories): void
    {
        if (!is_null($product->getId())) {
            $hasCategories = $product->getProductHasCategories();
            $hasCategories->clear();
        }

        $categoriesLocale = $this->categoryTranslationRepository->findBy(['slug' => $categories]);

        foreach ($categoriesLocale as $categoryTranslation) {
            $hasCategory = new ProductHasCategories();
            $hasCategory->setCategory($categoryTranslation->getCategory());
            $hasCategory->setProduct($product);

            $product->addProductHasCategory($hasCategory);
        }
    }

    /**
     * @param Product $product
     * @param array   $tags
     */
    private function setTags(Product $product, array $tags): void
    {
        if (!is_null($product->getId())) {
            $hasTags = $product->getProductHasTags();
            $hasTags->clear();
        }

        foreach ($tags as $tag) {
            $hasTags = new ProductHasTags();
            $hasTags->setTag($tag);

            $product->addProductHasTag($hasTags);
        }
    }

    /**
     * @param Product $product
     * @param array   $sizes
     */
    private function setSizes(Product $product, array $sizes): void
    {
        if (!is_null($product->getId())) {
            $hasTags = $product->getProductHasSizes();
            $hasTags->clear();
        }

        $sizeCollection = $this->sizeRepository->findBy(['slug' => $sizes]);

        foreach ($sizeCollection as $size) {
            $hasSize = new ProductHasSizes();
            $hasSize->setSize($size);
            $hasSize->setIsAvailable(true);

            $product->addProductHasSize($hasSize);
        }
    }
}