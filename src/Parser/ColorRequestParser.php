<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\ColorTranslation;
use App\Entity\ProductColor;
use App\Repository\ColorTranslationRepository;
use App\Request\Dto\Admin\ColorEditRequestDto;

final class ColorRequestParser
{
    public function __construct(
        private readonly ColorTranslationRepository $colorTranslationRepository,
        private readonly array $locales,
    ) {}

    /**
     * @param ProductColor|null $productColor
     *
     */
    public function parse(ColorEditRequestDto $colorEditRequestDto, ProductColor $productColor = null): ProductColor
    {
        if (!$productColor instanceof ProductColor) {
            $productColor = new ProductColor();
        }

        $productColor->setHex($colorEditRequestDto->hex);

        $this->setTranslations($productColor, $colorEditRequestDto);

        return $productColor;
    }

    private function setTranslations(ProductColor $productColor, ColorEditRequestDto $colorEditRequestDto): void
    {
        foreach ($this->locales as $locale) {
            $transCollection = $colorEditRequestDto->translations[$locale];

            $trans = $this->colorTranslationRepository->findOneBy(['color' => $productColor, 'locale' => $locale]);

            if (null === $trans) {
                $trans = new ColorTranslation();
                $trans->setLocale($locale);
            }

            $trans->setTitle($transCollection['title']);

            $productColor->addColorTranslation($trans);
        }
    }
}
