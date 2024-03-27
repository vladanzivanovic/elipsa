<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\OrderProduct;
use App\Entity\OrderProductTranslation;

final class OrderProductView
{
    private PriceView $priceView;

    private ImageView $imageView;

    private ColorView $colorView;

    private ProductView $productView;

    public function __construct(
        PriceView $priceView,
        ImageView $imageView,
        ColorView $colorView,
        ProductView $productView
    ) {
        $this->priceView = $priceView;
        $this->imageView = $imageView;
        $this->colorView = $colorView;
        $this->productView = $productView;
    }

    public function view(OrderProduct $orderProduct, string $locale): array
    {
        $discount = $orderProduct->getDiscount();
        $originalPrice = $orderProduct->getPrice();
        $quantity = $orderProduct->getQuantity();
        $product = $orderProduct->getProduct();

        $isSizeAvailable = $product->isSizeAvailable($orderProduct->getSize());

        $view = [
            'id' => $orderProduct->getId(),
            'quantity' => $quantity,
            'size' => $orderProduct->getSize(),
            'price' => $this->priceView->view($originalPrice, $locale),
            'discount' => [],
            'translation' => $this->getTranslationValues($orderProduct->getByLocale($locale)),
            'total' => $this->priceView->view($orderProduct->getTotal(), $locale),
            'total_original' => $this->priceView->view($orderProduct->getOriginalTotal(), $locale),
            'image' => $this->imageView->view(
                $orderProduct->getImage(),
                'product',
                'cart_thumb'
            ),
            'is_sold' => $product->isSold() || false === $isSizeAvailable,
            'color' => $this->colorView->productPageView($orderProduct->getColor()),
            'promotion_price' => [],
            'product' => $this->productView->view($product, $locale),
        ];

        if (0 < $discount) {
            $percentage = (int) round(abs((100 - ($discount/$originalPrice) * 100)));

            $view['discount'] = [
                'price' => $this->priceView->view($discount, $locale),
                'percentage' => 100 !== $percentage ? $percentage : 0,
                'saving' => $this->priceView->view($discount - $originalPrice, $locale),
            ];
        }

        if (null !== $orderProduct->getPromotionPrice()) {
            $view['promotion_price'] = $this->priceView->view($orderProduct->getPromotionPrice(), $locale);
        }

        return $view;
    }

    private function getTranslationValues(OrderProductTranslation $orderProductTranslation): array
    {
        return [
            'title' => $orderProductTranslation->getTitle(),
            'slug' => $orderProductTranslation->getSlug(),
        ];
    }
}
