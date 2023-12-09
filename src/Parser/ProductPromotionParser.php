<?php

declare(strict_types=1);

namespace App\Parser;

use App\Checker\PromotionCheckerInterface;
use App\Checker\PromotionCheckerTrait;
use App\Checker\PromotionOptionCheckerInterface;
use App\Collector\PromotionCollector;
use App\Entity\Product;
use App\Entity\Promotion;

final class ProductPromotionParser
{
    use PromotionCheckerTrait;

    private PromotionCollector $promotionCollector;

    /**
     * @var array <int, PromotionOptionCheckerInterface, PromotionCheckerInterface>
     */
    private array $promotionCheckers;

    public function __construct(
        PromotionCollector $promotionCollector,
        iterable $promotionCheckers
    ){
        $this->promotionCollector = $promotionCollector;
        $this->promotionCheckers = iterator_to_array($promotionCheckers);
    }

    /**
     * @param array|null $productPromotions - in ProductFormatter set the prop explicitly, so don't need to collect promotions every time in foreach loop
     */
    public function setProductPromotion(Product $product, array $productPromotions = null): ?Promotion
    {
        if (null === $productPromotions) {
            $productPromotions = $this->promotionCollector->collectProductPromotions();
        }

        foreach ($productPromotions as $productPromotionElements) {
            foreach ($productPromotionElements as $productPromotion) {
                if (false === $this->checkCouponIsEligible($productPromotion)) {
                    continue;
                }

                if (true === $this->checkCouponOptionsAreEligibleForProduct($product, $productPromotion)) {
                    $price = $product->getDiscount() > 0 ? $product->getDiscount() : $product->getPrice();

                    $discountAmount = $price * ((100 - $productPromotion->getDiscount()) / 100);

                    $product->setPromoDiscount((int)$discountAmount);

                    return $productPromotion;
                }
            }
        }

        return null;
    }
}
