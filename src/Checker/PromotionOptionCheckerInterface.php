<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\Promotion;
use App\Entity\PromotionOption;
use App\Entity\ShopOrder;

interface PromotionOptionCheckerInterface
{
    public function isEligible(OrderProduct $orderProduct, PromotionOption $promotionOption): bool;

    public function isProductEligible(Product $product, PromotionOption $promotionOption): bool;

    public function getType(): string;
}
