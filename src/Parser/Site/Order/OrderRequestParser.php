<?php

declare(strict_types=1);

namespace App\Parser\Site\Order;

use App\Entity\ShopOrder;
use App\Repository\ShopOrderRepository;
use Webmozart\Assert\Assert;

final class OrderRequestParser
{
    private ShopOrderRepository $orderRepository;

    public function __construct(
        ShopOrderRepository $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    public function findOrder(
        string $token
    ): ShopOrder {
        $order = $this->orderRepository->findOneBy(['token' => $token]);

        Assert::notNull($order);

        return $order;
    }

    public function create():ShopOrder
    {
        $order = new ShopOrder();
        $order->setStatus(ShopOrder::STATUS_NEW);
        $order->setToken();

        return $order;
    }
}
