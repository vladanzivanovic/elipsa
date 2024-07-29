<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\ProductCleaning;
use App\Entity\ProductHasCategories;
use App\Entity\ProductHasSizes;
use App\Entity\ProductHasTags;
use App\Entity\ProductOptions;
use App\Entity\ProductTranslation;
use App\Entity\Promotion;
use App\Entity\ShopOrder;
use App\Entity\Tags;
use App\Entity\Youtube;
use App\Parser\Site\Order\OrderCouponParser;
use App\Parser\Site\Order\OrderProductTranslationParser;
use App\Repository\CategoryTranslationRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\ProductTranslationRepository;
use App\Repository\TagsRepository;
use App\Request\Dto\Admin\ProductEditRequestDto;
use App\Services\ProductImageService;
use Gedmo\Sluggable\Util\Urlizer;

final class ProductEditRequestParser
{
    public function __construct(
        private readonly ProductTranslationRepository $translationRepository,
        private readonly CategoryTranslationRepository $categoryTranslationRepository,
        private readonly ProductSizeRepository $sizeRepository,
        private readonly ProductImageService $imageService,
        private readonly YouTubeParser $youTubeParser,
        private readonly TagsRepository $tagsRepository,
        private readonly OrderProductTranslationParser $orderProductTranslationParser,
        private readonly OrderCouponParser $orderCouponParser,
        private readonly array $locales,
    ) {}

    public function parse(ProductEditRequestDto $productEditRequestDto, null|Product $product = null): Product
    {
        if (!$product instanceof Product) {
            $product = new Product();
            $product->setStatus(Product::STATUS_PENDING);
        }

        $product->setCode($productEditRequestDto->code);
        $product->setAvailableCountries($productEditRequestDto->availableCountries);

        $this->setOptions($productEditRequestDto, $product);

        $this->setLocales($productEditRequestDto, $product);

        if ($productEditRequestDto->categories) {
            $this->setCategories($product, $productEditRequestDto->categories);
        }
        if ($productEditRequestDto->tags) {
            $this->setTags($product, $productEditRequestDto->tags);
        }

        $this->setCleaning($product, $productEditRequestDto);

        $this->imageService->setImages($product->getProductTranslations()->first(), $productEditRequestDto->images);

        $this->setYoutube($productEditRequestDto, $product);

        $this->updateOrderProducts($product);

        return $product;
    }

    public function setYoutube(ProductEditRequestDto $productEditRequestDto, Product $product): void
    {
        $product->getYoutubes()->clear();

        foreach ($productEditRequestDto->youtubeUrl as $youtube) {
            $youtube = $this->youTubeParser->parse($youtube);

            if (!$youtube instanceof Youtube) {
                continue;
            }

            $product->addYoutube($youtube);
        }
    }

    private function setLocales(ProductEditRequestDto $productEditRequestDto, Product $product): void
    {
        foreach ($this->locales as $locale) {
            $transCollection = $productEditRequestDto->translations[$locale] ?? null;

            if (null === $transCollection) {
                continue;
            }

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

    private function setSizes(ProductOptions $productOptions, array $sizes): void
    {
        $productOptions->getProductHasSizes()->clear();

        foreach ($sizes['slug'] as $index => $sizeSlug) {
            $size = $this->sizeRepository->findOneBy(['slug' => $sizeSlug]);

            $hasSize = new ProductHasSizes();
            $hasSize->setSize($size);
            $hasSize->setQuantity((int) $sizes['quantity'][$index]);

            $productOptions->addProductHasSize($hasSize);
        }
    }

    private function setCleaning(Product $product, ProductEditRequestDto $productEditRequestDto): void
    {
        $product->getProductCleanings()->clear();

        foreach ($productEditRequestDto->cleaning as $iconName) {
            $cleaning = new ProductCleaning();
            $cleaning->setIcon($iconName);
            $cleaning->setProduct($product);

            $product->addProductCleaning($cleaning);
        }
    }

    private function updateOrderProducts(Product $product): void
    {
        foreach ($product->getOrderProducts() as $orderProduct) {
            $order = $orderProduct->getOrderId();

            if (ShopOrder::STATUS_NEW !== $order->getStatus()) {
                continue;
            }

            $orderProduct->setPrice($product->getPrice($order->getCountry()));
            $orderProduct->setDiscount($product->getDiscount($order->getCountry()));

            if ($order->getCoupon() instanceof Promotion) {
                $this->orderCouponParser->setPromotionPriceOnOrderItems($order->getCoupon(), $orderProduct);
            }

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

    private function setOptions(ProductEditRequestDto $productEditRequestDto, Product $product): void
    {
        $product->getProductOptions()->clear();

        foreach ($productEditRequestDto->options as $countryCode => $optionsDto) {
            if (false === in_array($countryCode, $productEditRequestDto->availableCountries)) {
                continue;
            }

            if (null === $optionsDto->price) {
                continue;
            }

            $productOptions = new ProductOptions();
            $productOptions->setPrice($optionsDto->price);
            $productOptions->setDiscount($optionsDto->discount);
            $productOptions->setSold($optionsDto->isSold);
            $productOptions->setCountry($countryCode);

            $this->setSizes($productOptions, $optionsDto->sizes);

            $homePageArray = [];

            if (null !== $optionsDto->showHomePage) {
                foreach ($optionsDto->showHomePage as $homePage) {
                    $homePageArray[$homePage['home_page_position']] = (int)$homePage['slider_position'];
                }
            }

            $productOptions->setShowHomePage($homePageArray);

            $product->addProductOption($productOptions);
        }
    }
}
