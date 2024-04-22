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

    private array $locales;

    public function __construct(
        private readonly CategoryTranslationRepository $categoryTranslationRepository,
        private readonly TagsRepository $tagsRepository,
        private readonly ProductSizeRepository $sizeRepository,
        private readonly ProductHasImagesRepository $hasImagesRepository,
        private readonly ImageRepository $imageRepository,
        private readonly RouterInterface $router,
        private readonly string $defaultLocale,
        string $locales,
    ) {
        $this->locales = explode('|', $locales);
    }

    
    public function formatResponse(Catalogue $catalogue): array
    {
        $view = [
            'selectedImages' => $this->imagesFormatter($this->router, $this->imageRepository->getByCatalog($catalogue), 'catalog'),
        ];

        $translations = [];

        foreach ($this->locales as $locale) {
            $trans = $catalogue->getByLocale($locale);

            if (null === $trans) {
                $trans = $catalogue->getByLocale($this->defaultLocale);
            }

            $translations[$locale] = [
                'title' => $trans->getTitle(),
            ];
        }

        $view['translations'] = $translations;

        return $view;
    }
}
