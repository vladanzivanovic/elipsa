<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Banner;
use App\Entity\Promotion;
use App\Formatter\Options\CategoryOptionsFormatter;
use App\Formatter\Options\ColorsOptionsFormatter;
use App\Formatter\Options\PromotionTypeOptions;
use App\Formatter\Options\TagOptionsFormatter;
use App\View\PromotionCouponView;
use Symfony\Component\Routing\RouterInterface;

final class CouponEditResponseFormatter
{
    public function __construct(
        private readonly PromotionCouponView $couponView,
        private readonly CategoryOptionsFormatter $categoryOptionsFormatter,
        private readonly TagOptionsFormatter $tagOptionsFormatter,
        private readonly PromotionTypeOptions $promotionTypeOptions,
        private readonly ColorsOptionsFormatter $colorsOptionsFormatter,
        private readonly string $defaultLocale
    ) {}

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
            'colors' => $this->colorsOptionsFormatter->format(),
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
