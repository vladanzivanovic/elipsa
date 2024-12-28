<?php

declare(strict_types=1);

namespace App\EventListener;
;
use App\Entity\Promotion;
use App\Event\OrderProductPromotionEvent;
use App\Exception\CouponCheckerException;
use App\Handler\Site\OrderHandler;
use App\Parser\ProductPromotionParser;
use App\Parser\Site\Order\OrderCouponParser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class OrderProductPromotionEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ProductPromotionParser $productPromotionParser,
        private readonly OrderCouponParser $orderCouponParser,
        private readonly OrderHandler $orderHandler,
    ){}

    public static function getSubscribedEvents(): array
    {
        return [
            OrderProductPromotionEvent::ORDER_PRODUCT_PROMOTION => [
                ['manageProductPromotion', 0],
            ],
        ];
    }

    public function manageProductPromotion(OrderProductPromotionEvent $orderProductPromotionEvent): void
    {
        $order = $orderProductPromotionEvent->getOrder();

        $isCouponInvalid = false;
        $isPriceChanged = false;

        foreach ($order->getOrderProducts() as $orderProduct) {
            $product = $orderProduct->getProduct();
            $productOption = $product->getOptionsByCountry($order->getCountry());

            $originDiscountPrice = $orderProduct->getDiscount();

            $productPromotion = $this->productPromotionParser->setProductPromotion($product, $order->getCountry());

            $orderProduct->setDiscount($productOption->getPromoDiscount() ?? $productOption->getDiscount());

            $isPriceChanged = $originDiscountPrice !== $orderProduct->getDiscount();

            if ($productPromotion instanceof Promotion) {
                $orderProduct->setPromotion($productPromotion);
            }

            if ($order->getCoupon() instanceof Promotion) {
                $oldPromotionPrice = $orderProduct->getPromotionPrice();
                try {
                    $isSetPromotionPrice = $this->orderCouponParser->setPromotionPriceOnOrderItems($order->getCoupon(), $orderProduct);

                    if (false === $isSetPromotionPrice) {
                        $orderProduct->setPromotionPrice(null);
                        $isCouponInvalid = true;
                    }

                } catch (CouponCheckerException $couponCheckerException) {
                    $orderProduct->setPromotionPrice(null);

                    $isCouponInvalid = true;
                }

                if ($oldPromotionPrice !== $orderProduct->getPromotionPrice()) {
                    $isPriceChanged = true;
                }
            }

            $order->addOrderProduct($orderProduct);
        }

        if (true === $isCouponInvalid) {
            $order->setCoupon(null);
        }

        $order->setOrderPriceChanged($isPriceChanged);

        $this->orderHandler->save($order);
    }
}
