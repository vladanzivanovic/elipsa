<?php

declare(strict_types=1);

namespace App\Parser\Site\Order;

use App\Checker\PromotionCheckerTrait;
use App\Checker\PromotionCouponChecker;
use App\Checker\PromotionValidityChecker;
use App\Entity\OrderProduct;
use App\Entity\Promotion;
use App\Entity\ShopOrder;
use App\Exception\OrderException;
use App\Repository\PromotionRepository;
use Webmozart\Assert\Assert;

final class OrderCouponParser
{
    private PromotionRepository $promotionCouponRepository;

    private OrderRequestParser $orderRequestParser;
    private PromotionCouponChecker $promotionCouponChecker;

    public function __construct(
        PromotionRepository $promotionCouponRepository,
        OrderRequestParser $orderRequestParser,
        PromotionCouponChecker $promotionCouponChecker
    ) {
        $this->promotionCouponRepository = $promotionCouponRepository;
        $this->orderRequestParser = $orderRequestParser;
        $this->promotionCouponChecker = $promotionCouponChecker;
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
        Promotion $coupon,
        OrderProduct $orderProduct
    ): bool {
        if (false === $this->promotionCouponChecker->checkEligibility($orderProduct, $coupon)) {
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
        ?Promotion $promotionCoupon
    ): void {
        if (null === $promotionCoupon) {
            throw new OrderException('promo_coupon.not_found');
        }

        $isPromotionApplicable = false;

        foreach ($order->getOrderProducts() as $orderProduct) {
            if (true === $this->setPromotionPriceOnOrderItems($promotionCoupon, $orderProduct)) {
                $order->setCoupon($promotionCoupon);

                $isPromotionApplicable = true;
            }
        }

        if (false === $isPromotionApplicable) {
            throw new OrderException('promo_coupon.not_applicable');
        }
    }

    /**
     * @throws OrderException
     */
    private function removePromotion(
        ShopOrder $order,
        Promotion $promotionCoupon
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
}
