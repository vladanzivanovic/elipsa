<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Banner;
use App\Entity\PromotionCoupon;
use App\Formatter\Options\CategoryOptionsFormatter;
use App\Formatter\Options\TagOptionsFormatter;
use App\View\PromotionCouponView;
use Symfony\Component\Routing\RouterInterface;

final class CouponEditResponseFormatter
{
    private PromotionCouponView $couponView;

    private CategoryOptionsFormatter $categoryOptionsFormatter;

    private TagOptionsFormatter $tagOptionsFormatter;

    private string $defaultLocale;

    public function __construct(
        PromotionCouponView $couponView,
        CategoryOptionsFormatter $categoryOptionsFormatter,
        TagOptionsFormatter $tagOptionsFormatter,
        string $defaultLocale
    ) {
        $this->couponView = $couponView;
        $this->defaultLocale = $defaultLocale;
        $this->categoryOptionsFormatter = $categoryOptionsFormatter;
        $this->tagOptionsFormatter = $tagOptionsFormatter;
    }

    public function formatResponse(PromotionCoupon $coupon = null): array
    {
        $payload = [];

        $categories = array_map(function ($category) {
            $category['value'] = $category['id'];

            return $category;
        }, $this->categoryOptionsFormatter->format($this->defaultLocale));

        $options = [
            'categories' => $categories,
            'tags' => $this->tagOptionsFormatter->formatTagOptions(),
        ];

        if (null !== $coupon) {
            $payload = $this->couponView->editView($coupon);
        }

        return [
            'payload' => $payload,
            'options' => $options,
        ];
    }
}
