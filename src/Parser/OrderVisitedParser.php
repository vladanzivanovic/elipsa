<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\ShopOrder;
use App\Parser\Site\Order\OrderRequestParser;
use App\Request\Dto\OrderRequestDto;

final class OrderVisitedParser
{
    private OrderRequestParser $orderRequestParser;

    public function __construct(
       OrderRequestParser $orderRequestParser
    ) {
        $this->orderRequestParser = $orderRequestParser;
    }

    public function parse(OrderRequestDto $orderRequestDto): ShopOrder
    {
        $order = $this->orderRequestParser->findOrder($orderRequestDto->token);

        $order->setVisited(true);

        return $order;
    }
}
