<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\ProductCleaning;
use App\Entity\ProductHasCategories;
use App\Entity\ProductHasSizes;
use App\Entity\ProductHasTags;
use App\Entity\ProductTranslation;
use App\Entity\ShopOrder;
use App\Entity\Tags;
use App\Parser\Site\Order\OrderProductTranslationParser;
use App\Repository\CategoryTranslationRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\ProductTranslationRepository;
use App\Repository\TagsRepository;
use App\Services\ProductImageService;
use Gedmo\Sluggable\Util\Urlizer;
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

    private TagsRepository $tagsRepository;

    private OrderProductTranslationParser $orderProductTranslationParser;

    private array $locales;

    public function __construct(
        ParameterBagInterface $parameterBag,
        ProductTranslationRepository $translationRepository,
        CategoryTranslationRepository $categoryTranslationRepository,
        ProductSizeRepository $sizeRepository,
        ProductImageService $imageService,
        YouTubeParser $youTubeParser,
        TagsRepository $tagsRepository,
        OrderProductTranslationParser $orderProductTranslationParser,
        array $locales
    ) {
        $this->parameterBag = $parameterBag;
        $this->translationRepository = $translationRepository;
        $this->categoryTranslationRepository = $categoryTranslationRepository;
        $this->sizeRepository = $sizeRepository;
        $this->imageService = $imageService;
        $this->youTubeParser = $youTubeParser;
        $this->locales = $locales;
        $this->tagsRepository = $tagsRepository;
        $this->orderProductTranslationParser = $orderProductTranslationParser;
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
        $product->setSold($bag->getBoolean('sold'));

        $this->setLocales($bag, $product);

        if ($bag->has('categories')) {
            $this->setCategories($product, $bag->all('categories'));
        }
        if ($bag->has('tags')) {
            $this->setTags($product, $bag->all('tags'));
        }
        if ($bag->has('sizes')) {
            $this->setSizes($product, $bag->all('sizes'));
        }

        $this->setCleaning($product, $bag);

        $this->imageService->setImages($product->getProductTranslations()->first(), json_decode($bag->get('images'), true));

        $this->setYoutube($bag, $product);

        $this->updateOrderProducts($product);

        return $product;
    }

    public function setYoutube(ParameterBag $bag, Product $product): void
    {
        $collection = $product->getYoutubes();
        $collection->clear();

        if (false === $bag->has('youtube')) {
            return;
        }

        foreach ($bag->all('youtube') as $youtube) {
            $youtube = $this->youTubeParser->parse(json_decode($youtube, true));

            if (!$youtube instanceof \App\Entity\Youtube) {
                continue;
            }

            $product->addYoutube($youtube);
        }
    }

    private function setLocales(ParameterBag $bag, Product $product): void
    {
        foreach ($this->locales as $locale) {
            $transCollection = $bag->all($locale);
            $trans = $this->translationRepository->findOneBy(['product' => $product, 'locale' => $locale]);

            if (null === $trans) {
                $trans = new ProductTranslation();
            }

            $trans->setTitle($transCollection['title']);
            $trans->setSlug(Urlizer::urlize($transCollection['title'], '-'));
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

    private function setTags(Product $product, array $tagIds): void
    {
        if (!is_null($product->getId())) {
            $hasTags = $product->getProductHasTags();
            $hasTags->clear();
        }

        $tags = $this->tagsRepository->findBy(['id' => $tagIds]);

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

        foreach ($sizes['slug'] as $index => $sizeSlug) {
            $size = $this->sizeRepository->findOneBy(['slug' => $sizeSlug]);

            $hasSize = new ProductHasSizes();
            $hasSize->setSize($size);
            $hasSize->setQuantity((int) $sizes['quantity'][$index]);

            $product->addProductHasSize($hasSize);
        }
    }

    private function setCleaning(Product $product, ParameterBag $bag): void
    {
        $collection = $product->getProductCleanings();
        $collection->clear();

        if ($bag->has('cleaning')) {
            foreach ($bag->all('cleaning') as $iconName) {
                $cleaning = new ProductCleaning();
                $cleaning->setIcon($iconName);
                $cleaning->setProduct($product);

                $product->addProductCleaning($cleaning);
            }
        }
    }

    private function updateOrderProducts(Product $product): void
    {
        foreach ($product->getOrderProducts() as $orderProduct) {
            $order = $orderProduct->getOrderId();

            if (ShopOrder::STATUS_NEW !== $order->getStatus()) {
                continue;
            }

            $orderProduct->setPrice($product->getPrice());
            $orderProduct->setDiscount($product->getDiscount());

            $this->setTranslations($product, $orderProduct);
        }
    }

    private function setTranslations(Product $product, OrderProduct $orderProduct): void
    {
        foreach ($product->getProductTranslations() as $productTranslation) {
            $orderProductTranslation = $this->orderProductTranslationParser->parse($productTranslation, $orderProduct);

            $orderProduct->addOrderProductTranslation($orderProductTranslation);
        }
    }
}
