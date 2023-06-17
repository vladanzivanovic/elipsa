<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Product;
use App\Entity\ProductTranslation;
use App\Repository\CategoryTranslationRepository;
use App\Repository\ImageRepository;
use App\Repository\ProductCleaningRepository;
use App\Repository\ProductHasImagesRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\TagsRepository;
use Symfony\Component\Routing\RouterInterface;

final class ProductView
{
    private array $locales;
    private CategoryTranslationRepository $categoryTranslationRepository;
    private TagsRepository $tagsRepository;
    private ProductSizeRepository $sizeRepository;
    private ProductHasImagesRepository $hasImagesRepository;
    private ImageRepository $imageRepository;
    private RouterInterface $router;
    private ProductCleaningRepository $cleaningRepository;

    private ImageView $imageView;

    public function __construct(
        string $locales,
        CategoryTranslationRepository $categoryTranslationRepository,
        TagsRepository $tagsRepository,
        ProductSizeRepository $sizeRepository,
        ProductHasImagesRepository $hasImagesRepository,
        ImageRepository $imageRepository,
        RouterInterface $router,
        ProductCleaningRepository $cleaningRepository,
        ImageView $imageView
    ) {
        $this->locales = explode('|', $locales);
        $this->categoryTranslationRepository = $categoryTranslationRepository;
        $this->tagsRepository = $tagsRepository;
        $this->sizeRepository = $sizeRepository;
        $this->hasImagesRepository = $hasImagesRepository;
        $this->imageRepository = $imageRepository;
        $this->router = $router;
        $this->cleaningRepository = $cleaningRepository;
        $this->imageView = $imageView;
    }

    public function editView(Product $product): array
    {
        $view = [
            'show_home_page' => $product->getShowHomePage(),
        ];

        foreach ($this->locales as $locale) {
            $view[$locale] = $this->getTranslationValues($product->getByLocale($locale));
        }

        return $view + $this->view($product);
    }

    public function singlePageView(Product $product, string $locale): array
    {
        $view = [];

        $translationView = $this->getTranslationValues($product->getByLocale($locale));

        return $view + $translationView + $this->view($product);
    }

    public function gridView(Product $product, string $locale): array
    {
        $translationView = $this->getTranslationValues($product->getByLocale($locale));

        return $translationView + $this->view($product);
    }

    public function view(Product $product): array
    {
        return [
            'id' => $product->getId(),
            'code' => $product->getCode(),
            'price' => $product->getPrice(),
            'discount' => $product->getDiscount(),
        ];
    }

    private function getTranslationValues(ProductTranslation $productTranslation): array
    {
        return [
            'title' => $productTranslation->getTitle(),
            'slug' => $productTranslation->getSlug(),
            'short_description' => $productTranslation->getShortDescription(),
            'description' => $productTranslation->getDescription(),
            'cleaning' => $productTranslation->getCleaning(),
        ];
    }
}
