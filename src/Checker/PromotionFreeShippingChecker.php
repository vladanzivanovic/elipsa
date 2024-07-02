<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\Promotion;
use App\Entity\PromotionOption;
use App\Entity\Resources\PromotionEligibilityInterface;
use App\Entity\ShopOrder;
use Webmozart\Assert\Assert;

final class PromotionFreeShippingChecker extends AbstractPromotionChecker
{
    /**
     * @var array <int, PromotionOptionCheckerInterface>
     */
    private array $promotionOptionCheckers;

    private PromotionOptionDiscountChecker $promotionOptionDiscountChecker;

    public function __construct(
        iterable $promotionCheckers,
        iterable $promotionOptionCheckers,
        PromotionOptionDiscountChecker $promotionOptionDiscountChecker
    ){
        $this->promotionOptionCheckers = iterator_to_array($promotionOptionCheckers);
        $this->promotionOptionDiscountChecker = $promotionOptionDiscountChecker;

        parent::__construct($promotionCheckers);
    }

    public function checkEligibility(PromotionEligibilityInterface $order, Promotion $promotionCoupon)
    {
        Assert::isInstanceOf($order, ShopOrder::class);

        if (false === $this->checkCouponIsEligible($promotionCoupon)) {
            return false;
        }

        $checkerTypes = $promotionCoupon->getOptionTypes();

        if ([] === $checkerTypes) {
            return true;
        }

        foreach ($order->getOrderProducts() as $orderProduct) {
            if (
                null !== $orderProduct->getDiscount() &&
                false === $this->promotionOptionDiscountChecker->isEligible($orderProduct, $promotionCoupon->getOptionByType(PromotionOption::OPTION_ALL_PRODUCTS))
            ) {
                continue;
            }

            foreach ($this->promotionOptionCheckers as $promotionOptionChecker) {
                if (in_array($promotionOptionChecker->getType(), $checkerTypes)) {
                    $isOptionApplicable = $promotionOptionChecker->isEligible($orderProduct, $promotionCoupon->getOptionByType($promotionOptionChecker->getType()));

                    if (true === $isOptionApplicable) {
                        return true;
                    }
                }
            }
        }
    }
}
