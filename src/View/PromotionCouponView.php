<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Promotion;
use App\Repository\ProductRepository;

final class PromotionCouponView
{
    public function __construct(
        private readonly PromotionOptionView $promotionOptionView,
        private readonly ProductRepository $productRepository,
        private readonly string $defaultLocale,
    ) {}

    public function editView(Promotion $promotionCoupon): array
    {
        $view = $this->view($promotionCoupon);

        foreach ($promotionCoupon->getPromotionOptions() as $promotionOption) {
            $option = $this->promotionOptionView->view($promotionOption);
            $type = $option['type'];

            if ($option['type'] === 'products') {
                $configuration = $option['configuration'];
                $option['configuration'] = [];

                $products = $this->productRepository->findBy(['id' => $configuration]);

                foreach ($products as $product) {
                    $trans = $product->getByLocale($this->defaultLocale);

                    $option['configuration'][] = ['id' => $product->getId(), 'text' => $trans->getTitle()];
                }
            }

            $view['options'][$type] = $option;
        }

        return $view;
    }

    public function view(Promotion $coupon): array
    {
        return [
            'code' => $coupon->getCode(),
            'percentage' => $coupon->getDiscount(),
            'valid_from' => $coupon->getValidFrom()->format('d.m.Y'),
            'valid_to' => $coupon->getValidTo()->format('d.m.Y'),
            'type' => $coupon->getType(),
            'available_countries' => $coupon->getAvailableCountries(),
        ];
    }
}
