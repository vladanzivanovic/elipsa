<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\PromotionCoupon;
use App\Repository\ProductRepository;

final class PromotionCouponView
{
    private PromotionOptionView $promotionOptionView;

    private ProductRepository $productRepository;

    public function __construct(
        PromotionOptionView $promotionOptionView,
        ProductRepository $productRepository
    ) {
        $this->promotionOptionView = $promotionOptionView;
        $this->productRepository = $productRepository;
    }

    public function editView(PromotionCoupon $promotionCoupon): array
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
                    $trans = $product->getByLocale('rs');

                    $option['configuration'][] = ['id' => $product->getId(), 'text' => $trans->getTitle()];
                }
            }

            $view['options'][$type] = $option;
        }

        return $view;
    }

    public function view(PromotionCoupon $coupon): array
    {
        $view = [
            'code' => $coupon->getCode(),
            'percentage' => $coupon->getDiscount(),
            'valid_from' => $coupon->getValidFrom()->format('d.m.Y'),
            'valid_to' => $coupon->getValidTo()->format('d.m.Y'),
        ];

        return $view;
    }
}
