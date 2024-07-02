<?php

declare(strict_types=1);

namespace App\Formatter\Site\Router;

use App\Repository\ProductTranslationRepository;

final class ProductPageRouterFormatter
{
    public function __construct(
        private readonly ProductTranslationRepository $productTranslationRepository,
        private readonly string $defaultLocale,
    ) {}

    public function localeFormatter(string $slug, string $locale): null|string
    {
        $fromTrans = $this->productTranslationRepository->findOneBy(['slug' => $slug]);

        $toTrans = $fromTrans->getProduct()->getByLocale($locale);

        if (null === $toTrans) {
            $toTrans = $fromTrans->getProduct()->getByLocale($this->defaultLocale);
        }

        return null !== $toTrans ? $toTrans->getSlug() : null;
    }
}
