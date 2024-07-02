<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Banner;
use App\Entity\Promotion;
use App\Formatter\Options\CategoryOptionsFormatter;
use App\Formatter\Options\PromotionTypeOptions;
use App\Formatter\Options\TagOptionsFormatter;
use App\View\PromotionCouponView;
use Symfony\Component\Routing\RouterInterface;

final class CouponEditResponseFormatter
{
    private PromotionCouponView $couponView;

    private CategoryOptionsFormatter $categoryOptionsFormatter;

    private TagOptionsFormatter $tagOptionsFormatter;

    private PromotionTypeOptions $promotionTypeOptions;

    private string $defaultLocale;

    public function __construct(
        PromotionCouponView $couponView,
        CategoryOptionsFormatter $categoryOptionsFormatter,
        TagOptionsFormatter $tagOptionsFormatter,
        PromotionTypeOptions $promotionTypeOptions,
        string $defaultLocale
    ) {
        $this->couponView = $couponView;
        $this->defaultLocale = $defaultLocale;
        $this->categoryOptionsFormatter = $categoryOptionsFormatter;
        $this->tagOptionsFormatter = $tagOptionsFormatter;
        $this->promotionTypeOptions = $promotionTypeOptions;
    }

    public function formatResponse(Promotion $coupon = null): array
    {
        $payload = [];

        $categories = array_map(function ($category) {
            $category['value'] = $category['id'];

            return $category;
        }, $this->categoryOptionsFormatter->format($this->defaultLocale));

        $options = [
            'categories' => $categories,
            'tags' => $this->tagOptionsFormatter->formatTagOptions(),
            'promotion_types' => $this->promotionTypeOptions->format(),
        ];

        if ($coupon instanceof Promotion) {
            $payload = $this->couponView->editView($coupon);
        }

        return [
            'payload' => $payload,
            'options' => $options,
        ];
    }
}
