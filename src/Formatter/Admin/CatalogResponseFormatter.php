<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Catalogue;
use App\Entity\Image;
use App\Entity\Product;
use App\Entity\ProductHasCategories;
use App\Repository\CategoryTranslationRepository;
use App\Repository\ImageRepository;
use App\Repository\ProductHasImagesRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\TagsRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\RouterInterface;

final class CatalogResponseFormatter
{
    use ImageTrait;

    /**
     * @var CategoryTranslationRepository
     */
    private $categoryTranslationRepository;

    /**
     * @var TagsRepository
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
     * @param CategoryTranslationRepository $categoryTranslationRepository
     * @param TagsRepository                $tagsRepository
     * @param ProductSizeRepository         $sizeRepository
     * @param ProductHasImagesRepository    $hasImagesRepository
     * @param ImageRepository               $imageRepository
     * @param RouterInterface               $router
     */
    public function __construct(
        CategoryTranslationRepository $categoryTranslationRepository,
        TagsRepository $tagsRepository,
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
     * @param Catalogue $catalogue
     *
     * @return array
     */
    public function formatResponse(Catalogue $catalogue): array
    {
        $transRs = $catalogue->getByLocale('rs');
        $transEn = $catalogue->getByLocale('en');

        return [
            'rs_title' => $transRs->getTitle(),
            'en_title' => $transEn->getTitle(),
            'selectedImages' => $this->imagesFormatter($this->router, $this->imageRepository->getByCatalog($catalogue), 'catalog'),
        ];
    }
}