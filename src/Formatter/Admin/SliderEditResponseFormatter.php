<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Product;
use App\Entity\Slider;
use App\Repository\CategoryTranslationRepository;
use App\Repository\ImageRepository;
use App\Repository\ProductHasImagesRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\ProductTagsRepository;
use Symfony\Component\Routing\RouterInterface;

final class SliderEditResponseFormatter
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
    public function formatResponse(Slider $slider): array
    {
        $rsTrans = $slider->getByLocale('rs');
        $enTrans = $slider->getByLocale('en');

        return [
            'rs_description' => $rsTrans->getDescription(),
            'rs_button' => $rsTrans->getButtonText(),
            'rs_link' => $rsTrans->getButtonLink(),
            'en_description' => $enTrans->getDescription(),
            'en_button' => $enTrans->getButtonText(),
            'en_link' => $enTrans->getButtonLink(),
            'position' => $slider->getTextPosition(),
            'selectedImages' => $this->imagesFormatter($this->router, [$this->getImage($slider)]),
        ];
    }

    /**
     * @param Slider $slider
     *
     * @return array
     */
    private function getImage(Slider $slider): array
    {
        $image = $slider->getImage();

        return [
            'id' => $image->getId(),
            'fileName' => $image->getName(),
            'isMain' => $image->getIsMain(),
        ];
    }
}