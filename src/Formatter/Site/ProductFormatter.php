<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Checker\PromotionCheckerTrait;
use App\Collector\PromotionCollector;
use App\Entity\Product;
use App\Entity\User;
use App\Parser\ProductPromotionParser;
use App\Repository\PromotionRepository;
use App\Repository\TagsRepository;
use App\View\CategoryView;
use App\View\CleaningView;
use App\View\ColorView;
use App\View\ImageView;
use App\View\ProductSizeView;
use App\View\ProductView;
use App\View\TagView;
use App\View\YoutubeView;
use Symfony\Component\Routing\RouterInterface;

final class ProductFormatter
{
    use FormatterTrait;
    use PromotionCheckerTrait;

    public function __construct(
        private readonly RouterInterface $router,
        private readonly ProductView $productView,
        private readonly ImageView $imageView,
        private readonly ColorView $colorView,
        private readonly CategoryView $categoryView,
        private readonly CleaningView $cleaningView,
        private readonly TagsRepository $tagsRepository,
        private readonly TagView $tagView,
        private readonly YoutubeView $youtubeView,
        private readonly ProductSizeView $productSizeView,
        private readonly PromotionRepository $promotionRepository,
        private readonly ProductPromotionParser $productPromotionParser,
        private readonly PromotionCollector $promotionCollector,
    ) {}

    public function formatResponse(array $data, string $locale, string $countryCode, null|User $user = null): array
    {
        return
            ['payload' => $this->formatApiResponse($data, $locale, $countryCode, $user)] +
            ['relatedProducts' => $this->getProducts($data['related_products'], $countryCode)]
        ;
    }

    public function formatApiResponse(array $data, string $locale, string $countryCode, null|User $user = null): array
    {
        /** @var Product $product */
        $product = $data['product'];

        $this->productPromotionParser->setProductPromotion($product, $countryCode);
        $this->productPromotionParser->setFreeShippingPromotionEnabled($product);

        $productView = $this->productView->view($product, $user);

        $productView['categories'] = $this->getCategories($product, $locale);
        $productView['media']['images'] = $this->getImages($product);
        $productView['colors'] = $this->getColors($product);
        $productView['cleaningIcons'] = $this->getCleaningIcons($product);
        $productView['media']['youtubes'] = $this->getYoutubes($product);
        $productView['tags'] = $this->getTags($product);

        return $productView;
    }

    /**
     * @param array<int, Product> $products
     * @param string $countryCode
     * @param User|null $user
     * @return array
     */
    public function getProducts(array $products, string $countryCode, null|User $user = null): array
    {
        $productsView = [];

        foreach ($products as $product) {
            $this->productPromotionParser->setProductPromotion($product, $countryCode);
            $this->productPromotionParser->setFreeShippingPromotionEnabled($product);

            $productView = $this->productView->view($product, $user);
            $productView['colors'] = $this->getColors($product);
            $productView['tags'] = $this->getTags($product);
            $productView['image'] = $this->imageView->view($product->getMainImage(), 'product', 'list_thumb');

            $productsView[] = $productView;
        }

        return $productsView;
    }

    private function getImages(Product $product): array
    {
        $images = [];

        foreach ($product->getProductHasImages() as $productHasImage) {
            $images[] = $this->imageView->productPageView($productHasImage);
        }

        return $images;
    }

    private function getColors(Product $product): array
    {
        $colors = [];

        foreach ($product->getProductColors() as $hex => $productColor) {
            $colors[$hex] = $this->colorView->view($productColor);
        }

        return $colors;
    }

    private function getCategories(Product $product, string $locale): array
    {
        $categories = [];

        foreach ($product->getCategories() as $category) {
            $categories[] = $this->categoryView->productPageView($category, $locale);
        }

        return $categories;
    }

    private function getCleaningIcons(Product $product): array
    {
        $icons = [];

        foreach ($product->getProductCleanings() as $productCleaning) {
            $icons[] = $this->cleaningView->productPageView($productCleaning);
        }

        return $icons;
    }

    private function getTags(Product $product): array
    {
        $formattedTags = [];

        foreach ($product->getProductHasTags() as $productHasTag) {
            $tag = $productHasTag->getTag();

            $formattedTags[] = $this->tagView->view($tag);
        }

        return $formattedTags;
    }

    private function getYoutubes(Product $product): array
    {
        $youtubes = [];

        foreach ($product->getYoutubes() as $youtube) {
            $youtubes[] = $this->youtubeView->view($youtube);
        }

        return $youtubes;
    }
}
