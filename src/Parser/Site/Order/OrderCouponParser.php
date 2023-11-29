<?php

declare(strict_types=1);

namespace App\Parser\Site\Order;

use App\Checker\PromotionValidityChecker;
use App\Entity\OrderProduct;
use App\Entity\PromotionCoupon;
use App\Entity\ShopOrder;
use App\Exception\OrderException;
use App\Repository\PromotionCouponRepository;
use Webmozart\Assert\Assert;

final class OrderCouponParser
{
    private PromotionCouponRepository $promotionCouponRepository;

    private OrderRequestParser $orderRequestParser;

    /**
     * @var array[int, PromotionCheckerInterface]
     */
    private array $promotionCheckers;

    public function __construct(
        PromotionCouponRepository $promotionCouponRepository,
        OrderRequestParser $orderRequestParser,
        iterable $promotionCheckers
    ) {
        $this->promotionCouponRepository = $promotionCouponRepository;
        $this->orderRequestParser = $orderRequestParser;
        $this->promotionCheckers = iterator_to_array($promotionCheckers);
    }

    /**
     * @throws OrderException
     */
    public function parse(string $orderToken, string $couponCode, bool $removeCoupon = false): ShopOrder
    {
        $order = $this->orderRequestParser->findOrder($orderToken);

        $coupon = $this->promotionCouponRepository->findOneBy(['code' => $couponCode]);

        if (true === $removeCoupon) {
            $this->removePromotion($order, $coupon);

            return $order;
        }

        $this->addPromotion($order, $coupon);

        return $order;
    }

    public function setPromotionPriceOnOrderItems(
        PromotionCoupon $coupon,
        OrderProduct $orderProduct
    ): bool {
        if (false === $this->checkCouponOptionsAreEligible($orderProduct, $coupon)) {
            return false;
        }

        $couponPrice = $coupon->getDiscount() / 100;
        $price = 0 < $orderProduct->getDiscount() ? $orderProduct->getDiscount() : $orderProduct->getPrice();

        $promotionPrice = $price * $couponPrice;

        $orderProduct->setPromotionPrice((int) -$promotionPrice);

        return true;
    }

    /**
     * @throws OrderException
     */
    private function addPromotion(
        ShopOrder $order,
        ?PromotionCoupon $promotionCoupon
    ): void {
        if (null === $promotionCoupon) {
            throw new OrderException('promo_coupon.not_found');
        }

        $this->checkCouponIsEligible($order, $promotionCoupon);

        $isPromotionApplied = false;

        foreach ($order->getOrderProducts() as $orderProduct) {
            $isPromotionApplied = $this->setPromotionPriceOnOrderItems($promotionCoupon, $orderProduct);
        }

        if (true === $isPromotionApplied) {
            $order->setCoupon($promotionCoupon);

            return;
        }

        throw new OrderException('promo_coupon.not_applicable');
    }

    /**
     * @throws OrderException
     */
    private function removePromotion(
        ShopOrder $order,
        PromotionCoupon $promotionCoupon
    ): void {
        if(null === $order->getCoupon()) {
            throw new OrderException('order.promo_coupon_not_exists');
        }

        Assert::same($order->getCoupon(), $promotionCoupon);

        $order->setCoupon(null);

        foreach ($order->getOrderProducts() as $orderProduct) {
            $orderProduct->setPromotionPrice(null);
        }
    }

    private function checkCouponIsEligible(ShopOrder $order, PromotionCoupon $promotionCoupon)
    {
        $checkerTypes = [PromotionCoupon::TYPE_VALIDITY];

        foreach ($this->promotionCheckers as $promotionChecker) {
            if (true === in_array($promotionChecker->getType(), $checkerTypes)) {
                $promotionChecker->isEligible($order, $promotionCoupon);
            }
        }
    }

    private function checkCouponOptionsAreEligible(OrderProduct $orderProduct, PromotionCoupon $promotionCoupon): bool
    {
        $checkerTypes = $promotionCoupon->getOptionTypes();

        if (null === $checkerTypes) {
            return true;
        }

        $isOptionApplicable = false;

        foreach ($this->promotionCheckers as $promotionChecker) {
            if (true === in_array($promotionChecker->getType(), $checkerTypes)) {
                $isOptionApplicable = $promotionChecker->isEligible($orderProduct, $promotionCoupon->getOptionByType($promotionChecker->getType()));

                if (false === $isOptionApplicable) {
                    return false;
                }
            }
        }

        return $isOptionApplicable;
    }
}
