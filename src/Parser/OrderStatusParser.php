<?php

declare(strict_types=1);

namespace App\Parser;

use App\Parser\Site\Order\OrderRequestParser;
use App\Request\Dto\OrderStateRequestDto;

final class OrderStatusParser
{
    private OrderRequestParser $orderRequestParser;

    public function __construct(
       OrderRequestParser $orderRequestParser
    ) {
        $this->orderRequestParser = $orderRequestParser;
    }

    public function parse(OrderStateRequestDto $orderStateRequestDto)
    {
        $order = $this->orderRequestParser->findOrder($orderStateRequestDto->token);

        $order->setStatus($orderStateRequestDto->status);
        $order->setTrackingInfo($orderStateRequestDto->trackingInfo);

        return $order;
    }
}
