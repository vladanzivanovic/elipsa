<?php

declare(strict_types=1);

namespace App\Handler\Site;

use App\Entity\OrderProduct;
use App\Repository\OrderProductRepository;

final class OrderProductHandler
{
    private OrderProductRepository $orderProductRepository;

    public function __construct(
        OrderProductRepository $orderProductRepository
    ) {

        $this->orderProductRepository = $orderProductRepository;
    }

    public function removeProduct(OrderProduct $orderProduct): void
    {
        $this->orderProductRepository->removeWithFlush($orderProduct);
    }
}
