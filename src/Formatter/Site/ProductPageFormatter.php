<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Entity\Product;
use App\Repository\TagsRepository;
use App\View\CategoryView;
use App\View\CleaningView;
use App\View\ColorView;
use App\View\ImageView;
use App\View\ProductView;
use App\View\SizeView;
use App\View\TagView;
use App\View\YoutubeView;
use Symfony\Component\Routing\RouterInterface;

final class ProductPageFormatter
{
    use FormatterTrait;
    /**
     * @var RouterInterface
     */
    private $router;

    private ProductView $productView;

    private ImageView $imageView;

    private ColorView $colorView;

    private SizeView $sizeView;

    private CategoryView $categoryView;

    private CleaningView $cleaningView;

    private TagsRepository $tagsRepository;

    private TagView $tagView;

    private YoutubeView $youtubeView;

    public function __construct(
        RouterInterface $router,
        ProductView $productView,
        ImageView $imageView,
        ColorView $colorView,
        SizeView $sizeView,
        CategoryView $categoryView,
        CleaningView $cleaningView,
        TagsRepository $tagsRepository,
        TagView $tagView,
        YoutubeView $youtubeView
    ) {
        $this->router = $router;
        $this->productView = $productView;
        $this->imageView = $imageView;
        $this->colorView = $colorView;
        $this->sizeView = $sizeView;
        $this->categoryView = $categoryView;
        $this->cleaningView = $cleaningView;
        $this->tagsRepository = $tagsRepository;
        $this->tagView = $tagView;
        $this->youtubeView = $youtubeView;
    }

    public function formatResponse(array $data, string $locale): array
    {
        /** @var Product $product */
        $product = $data['product'];

        $productView = $this->productView->singlePageView($product, $locale);

        $productView['categories'] = $this->getCategories($product, $locale);
        $productView['images'] = $this->getImages($product);
        $productView['colors'] = $this->getColors($product);
        $productView['sizes'] = $this->getSizes($product);
        $productView['cleaningIcons'] = $this->getCleaningIcons($product);
        $productView['youtubes'] = $this->getYoutubes($product);

        return
            ['payload' => $productView] +
            ['relatedProducts' => $this->getRelatedProducts($data['related_products'], $locale)]
        ;
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
            $colors[$hex] = $this->colorView->productPageView($productColor);
        }

        return $colors;
    }

    private function getSizes(Product $product): array
    {
        $sizes = [];

        foreach ($product->getAvailableSizes() as $availableSize) {
            $sizes[] = $this->sizeView->productPageView($availableSize);
        }

        return $sizes;
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

    private function getTags(Product $product, string $locale): array
    {
        $tagSlugs = [];

        foreach ($product->getProductHasTags() as $productHasTag) {
            $tagSlugs[] = $productHasTag->getTag();
        }

        $tags = $this->tagsRepository->findBy(['mainSlug' => $tagSlugs, 'locale' => $locale]);

        $formattedTags = [];

        foreach ($tags as $tag) {
            $formattedTags[] = $this->tagView->view($tag);
        }

        return $formattedTags;
    }

    /**
     * @param array<int, Product> $relatedProducts
     * @return array<int, mixed>
     */
    private function getRelatedProducts(array $relatedProducts, string $locale): array
    {
        $products = [];

        foreach ($relatedProducts as $relatedProduct) {
            $product = $this->productView->gridView($relatedProduct, $locale);
            $product['colors'] = $this->getColors($relatedProduct);
            $product['sizes'] = $this->getSizes($relatedProduct);
            $product['tags'] = $this->getTags($relatedProduct, $locale);
            $product['image'] = $this->imageView->view($relatedProduct->getMainImage(), 'product', 'list_thumb');

            $products[] = $product;
        }

        return $products;
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
