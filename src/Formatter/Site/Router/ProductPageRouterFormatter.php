<?php

declare(strict_types=1);

namespace App\Formatter\Site\Router;

use App\Repository\ProductTranslationRepository;

final class ProductPageRouterFormatter
{
    private ProductTranslationRepository $productTranslationRepository;

    public function __construct(
        ProductTranslationRepository $productTranslationRepository
    ) {
        $this->productTranslationRepository = $productTranslationRepository;
    }

    /**
     * @param string $slug
     * @param string $locale
     *
     * @return string
     */
    public function localeFormatter(string $slug, string $locale): ?string
    {
        $fromTrans = $this->productTranslationRepository->findOneBy(['slug' => $slug]);

        $toTrans = $fromTrans->getProduct()->getByLocale($locale);

        return null !== $toTrans ? $toTrans->getSlug() : null;
    }
}
