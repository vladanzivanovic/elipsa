<?php

declare(strict_types=1);

namespace App\Parser\Site\Order;

use App\Entity\ShopOrder;
use Symfony\Component\HttpFoundation\ParameterBag;

final class OrderFinishParser
{
    private OrderRequestParser $orderRequestParser;

    public function __construct(
        OrderRequestParser $orderRequestParser
    ) {
        $this->orderRequestParser = $orderRequestParser;
    }
    public function parse(string $orderToken, ?ParameterBag $bag = null): ShopOrder
    {
        $order = $this->orderRequestParser->findOrder($orderToken);

        $order->setStatus(ShopOrder::STATUS_COMPLETED);

        if ($order->getPaymentType() === ShopOrder::PAYMENT_TYPE_CREDIT_CARD) {
            $order->setTransactionData([ShopOrder::CARD_TYPE_PRE_AUTH => $bag->all()]);
            $order->setStatus(ShopOrder::STATUS_AWAITING_AUTHORIZATION);
        }

        return $order;
    }
}
