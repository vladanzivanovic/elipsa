<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Entity\Product;
use App\Entity\User;
use App\Repository\TagsRepository;
use App\View\CategoryView;
use App\View\CleaningView;
use App\View\ColorView;
use App\View\ImageView;
use App\View\ProductSizeView;
use App\View\ProductView;
use App\View\SizeView;
use App\View\TagView;
use App\View\YoutubeView;
use Symfony\Component\Routing\RouterInterface;

final class ProductFormatter
{
    use FormatterTrait;

    private RouterInterface $router;

    private ProductView $productView;

    private ImageView $imageView;

    private ColorView $colorView;

    private CategoryView $categoryView;

    private CleaningView $cleaningView;

    private TagsRepository $tagsRepository;

    private TagView $tagView;

    private YoutubeView $youtubeView;

    private ProductSizeView $productSizeView;

    public function __construct(
        RouterInterface $router,
        ProductView $productView,
        ImageView $imageView,
        ColorView $colorView,
        CategoryView $categoryView,
        CleaningView $cleaningView,
        TagsRepository $tagsRepository,
        TagView $tagView,
        YoutubeView $youtubeView,
        ProductSizeView $productSizeView
    ) {
        $this->router = $router;
        $this->productView = $productView;
        $this->imageView = $imageView;
        $this->colorView = $colorView;
        $this->categoryView = $categoryView;
        $this->cleaningView = $cleaningView;
        $this->tagsRepository = $tagsRepository;
        $this->tagView = $tagView;
        $this->youtubeView = $youtubeView;
        $this->productSizeView = $productSizeView;
    }

    public function formatResponse(array $data, string $locale, ?User $user = null): array
    {

        return
            ['payload' => $this->formatApiResponse($data, $locale, $user)] +
            ['relatedProducts' => $this->getProducts($data['related_products'], $locale)]
        ;
    }

    public function formatApiResponse(array $data, string $locale, ?User $user = null): array
    {
        /** @var Product $product */
        $product = $data['product'];

        $productView = $this->productView->view($product, $locale, $user);

        $productView['categories'] = $this->getCategories($product, $locale);
        $productView['media']['images'] = $this->getImages($product);
        $productView['colors'] = $this->getColors($product);
        $productView['sizes'] = $this->getSizes($product);
        $productView['cleaningIcons'] = $this->getCleaningIcons($product);
        $productView['media']['youtubes'] = $this->getYoutubes($product);
        $productView['tags'] = $this->getTags($product, $locale);

        return $productView;
    }

    /**
     * @param array<int, Product> $relatedProducts
     * @return array<int, mixed>
     */
    public function getProducts(array $relatedProducts, string $locale, ?User $user = null): array
    {
        $products = [];

        foreach ($relatedProducts as $relatedProduct) {
            $product = $this->productView->view($relatedProduct, $locale, $user);
            $product['colors'] = $this->getColors($relatedProduct);
            $product['sizes'] = $this->getSizes($relatedProduct);
            $product['tags'] = $this->getTags($relatedProduct, $locale);
            $product['image'] = $this->imageView->view($relatedProduct->getMainImage(), 'product', 'list_thumb');

            $products[] = $product;
        }

        return $products;
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

        foreach ($product->getProductHasSizes() as $productHasSize) {
            $sizes[] = $this->productSizeView->view($productHasSize);
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
