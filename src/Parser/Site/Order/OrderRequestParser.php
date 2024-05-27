<?php

declare(strict_types=1);

namespace App\Parser\Site\Order;

use App\Entity\ShopOrder;
use App\Repository\ShopOrderRepository;
use Webmozart\Assert\Assert;

final class OrderRequestParser
{
    public function __construct(
        private readonly ShopOrderRepository $orderRepository
    ) {}

    public function findOrder(
        string $token
    ): ShopOrder {
        $order = $this->orderRepository->findOneBy(['token' => $token]);

        Assert::notNull($order);

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
