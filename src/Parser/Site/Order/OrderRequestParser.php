<?php

declare(strict_types=1);

namespace App\Parser\Site\Order;

use App\Entity\ShopOrder;
use App\Event\OrderProductPromotionEvent;
use App\Repository\ShopOrderRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

final class OrderRequestParser
{
    public function __construct(
        private readonly ShopOrderRepository $orderRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    public function findOrder(
        string $token
    ): ShopOrder {
        $order = $this->orderRepository->findOneBy(['token' => $token]);

        Assert::notNull($order);

        $event = new OrderProductPromotionEvent($order);
        $this->eventDispatcher->dispatch($event, OrderProductPromotionEvent::ORDER_PRODUCT_PROMOTION);

        return $order;
    }

    public function create(string $country):ShopOrder
    {
        $order = new ShopOrder();
        $order->setStatus(ShopOrder::STATUS_NEW);
        $order->setToken();
        $order->setCountry($country);

        return $order;
    }
}
