<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Product;
use App\Entity\ProductCleaning;
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

    private ParameterBagInterface $parameterBag;

    private ProductTranslationRepository $translationRepository;

    private CategoryTranslationRepository $categoryTranslationRepository;

    private ProductSizeRepository $sizeRepository;

    private ProductImageService $imageService;

    private YouTubeParser $youTubeParser;

    private array $locales;

    public function __construct(
        ParameterBagInterface $parameterBag,
        ProductTranslationRepository $translationRepository,
        CategoryTranslationRepository $categoryTranslationRepository,
        ProductSizeRepository $sizeRepository,
        ProductImageService $imageService,
        YouTubeParser $youTubeParser,
        string $locales
    ) {
        $this->parameterBag = $parameterBag;
        $this->translationRepository = $translationRepository;
        $this->categoryTranslationRepository = $categoryTranslationRepository;
        $this->sizeRepository = $sizeRepository;
        $this->imageService = $imageService;
        $this->youTubeParser = $youTubeParser;
        $this->locales = explode('|', $locales);
    }

    public function parse(ParameterBag $bag, ?Product $product = null): Product
    {
        if (!$product instanceof Product) {
            $product = new Product();
            $product->setStatus(Product::STATUS_PENDING);
        }

        $product->setCode($bag->get('code'));
        $product->setDiscount($bag->getInt('discount'));
        $product->setPrice($bag->getInt('price'));
        $product->setShowHomePage((int) $bag->get('show_home_page'));

        $this->setLocales($bag, $product);

        if ($bag->has('categories')) {
            $this->setCategories($product, $bag->get('categories'));
        }
        if ($bag->has('tags')) {
            $this->setTags($product, $bag->get('tags'));
        }
        if ($bag->has('sizes')) {
            $this->setSizes($product, $bag->get('sizes'));
        }

        $this->setCleaning($product, $bag);

        $this->imageService->setImages($product->getProductTranslations()->first(), json_decode($bag->get('images'), true));

        $this->setYoutube($bag, $product);

        return $product;
    }

    private function setLocales(ParameterBag $bag, Product $product): void
    {
        foreach ($this->locales as $locale) {
            $transCollection = $bag->get($locale);
            $trans = new ProductTranslation();

            if (!is_null($product->getId())) {
                $trans = $this->translationRepository->findOneBy(['product' => $product, 'locale' => $locale]);
            }

            $trans->setTitle($transCollection['title']);
            $trans->setDescription($transCollection['description']);
            $trans->setShortDescription($transCollection['short_description']);
            $trans->setCleaning($transCollection['cleaning']);
            $trans->setLocale($locale);

            $product->addProductTranslation($trans);
        }
    }

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

    private function setCleaning(Product $product, ParameterBag $bag)
    {
        $collection = $product->getProductCleanings();
        $collection->clear();

        if ($bag->has('cleaning')) {
            foreach ($bag->get('cleaning') as $iconName) {
                $cleaning = new ProductCleaning();
                $cleaning->setIcon($iconName);
                $cleaning->setProduct($product);

                $product->addProductCleaning($cleaning);
            }
        }
    }

    public function setYoutube(ParameterBag $bag, Product $product): void
    {
        $collection = $product->getYoutubes();
        $collection->clear();

        if (false === $bag->has('youtube')) {
            return;
        }

        foreach ($bag->get('youtube') as $youtube) {
            $youtube = $this->youTubeParser->parse(json_decode($youtube, true));

            if (null === $youtube) {
                continue;
            }

            $product->addYoutube($youtube);
        }
    }
}
