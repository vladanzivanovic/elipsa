<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\ProductOptions;
use App\View\ImageView;
use App\View\Product\ProductOptionsView;

final class ProductHomePageFormatter
{
    public function __construct(
        private readonly ProductOptionsView $productOptionsView,
        private readonly ImageView $imageView,
    ) {}
    public function format(array $productOptionCollection): array
    {
        $formattedProducts = [];

        foreach ($productOptionCollection as $homePagePosition => $productOptionByPosition) {
            foreach ($productOptionByPosition as $countryCode => $productOptionsByCountry) {
                /** @var ProductOptions $productOption */
                foreach ($productOptionsByCountry as $productOption) {
                    $product = $productOption->getProduct();
                    $productTrans = $product->getByLocale($countryCode);

                    $view = $this->productOptionsView->view($productOption, $countryCode);
                    $view['title'] = $productTrans->getTitle();
                    $view['media']['image'] = $this->imageView->view($product->getMainImage(), 'product');

                    $formattedProducts[$homePagePosition][$countryCode][] = $view;
                }
            }
        }

        return [
            'payload' => $formattedProducts
        ];
    }
}
