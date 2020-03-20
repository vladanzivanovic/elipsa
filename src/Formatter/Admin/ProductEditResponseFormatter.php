<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Product;
use App\Entity\ProductHasCategories;
use App\Repository\CategoryTranslationRepository;
use App\Repository\ImageRepository;
use App\Repository\ProductHasImagesRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\ProductTagsRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\RouterInterface;

final class ProductEditResponseFormatter
{
    use ImageTrait;

    /**
     * @var CategoryTranslationRepository
     */
    private $categoryTranslationRepository;

    /**
     * @var ProductTagsRepository
     */
    private $tagsRepository;

    /**
     * @var ProductSizeRepository
     */
    private $sizeRepository;

    /**
     * @var ProductHasImagesRepository
     */
    private $hasImagesRepository;

    /**
     * @var ImageRepository
     */
    private $imageRepository;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * ProductEditResponseFormatter constructor.
     *
     * @param CategoryTranslationRepository $categoryTranslationRepository
     * @param ProductTagsRepository         $tagsRepository
     * @param ProductSizeRepository         $sizeRepository
     * @param ProductHasImagesRepository    $hasImagesRepository
     * @param ImageRepository               $imageRepository
     * @param RouterInterface               $router
     */
    public function __construct(
      CategoryTranslationRepository $categoryTranslationRepository,
        ProductTagsRepository $tagsRepository,
        ProductSizeRepository $sizeRepository,
        ProductHasImagesRepository $hasImagesRepository,
        ImageRepository $imageRepository,
        RouterInterface $router
    ) {
        $this->categoryTranslationRepository = $categoryTranslationRepository;
        $this->tagsRepository = $tagsRepository;
        $this->sizeRepository = $sizeRepository;
        $this->hasImagesRepository = $hasImagesRepository;
        $this->imageRepository = $imageRepository;
        $this->router = $router;
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    public function formatResponse(Product $product): array
    {
        $rsTrans = $product->getByLocale('rs');
        $enTrans = $product->getByLocale('en');

        $product = [
            'rs_title' => $rsTrans->getTitle(),
            'rs_short_description' => $rsTrans->getShortDescription(),
            'rs_description' => $rsTrans->getDescription(),
            'en_title' => $enTrans->getTitle(),
            'en_short_description' => $enTrans->getShortDescription(),
            'en_description' => $enTrans->getDescription(),
            'code' => $product->getCode(),
            'price' => $product->getPrice(),
            'discount' => $product->getDiscount(),
            'selectedCategories' => array_column($this->categoryTranslationRepository->getByProduct($product), 'slug'),
            'selectedTags' => array_column($this->tagsRepository->getByProduct($product), 'mainSlug'),
            'selectedSizes' => array_column($this->sizeRepository->getByProduct($product), 'slug'),
            'selectedImages' => $this->imagesFormatter($this->router, $this->imageRepository->getByProduct($product)),
        ];

        return $product;
    }
}