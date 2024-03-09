<?php

declare(strict_types=1);

namespace App\Parser\Site\Order;

use App\Entity\ShopOrder;
use App\Exception\OrderException;
use Symfony\Component\HttpFoundation\ParameterBag;

final class OrderFinishParser
{
    private OrderRequestParser $orderRequestParser;

    public function __construct(
        OrderRequestParser $orderRequestParser
    ) {
        $this->orderRequestParser = $orderRequestParser;
    }

    /**
     * @throws OrderException
     */
    public function parse(string $orderToken, bool $isSuccessfulTransaction, ?ParameterBag $bag = null): ShopOrder
    {
        $order = $this->orderRequestParser->findOrder($orderToken);

        if (null !== $order->getCompletedAt()) {
            throw new OrderException('order.already_completed');
        }

        if (true === $isSuccessfulTransaction) {
            $this->setSuccessfulTransactionData($order, $bag);
        } else {
            $this->setFailedTransactionData($order, $bag);
        }

        return $order;
    }

    private function setSuccessfulTransactionData(ShopOrder $order, ?ParameterBag $bag = null): void
    {
        $order->setStatus(ShopOrder::STATUS_PENDING);
        $order->setCompletedAt(new \DateTime());

        if ($order->getPaymentType() === ShopOrder::PAYMENT_TYPE_CREDIT_CARD) {
            $order->setTransactionData([ShopOrder::CARD_STATUS_PRE_AUTH => $bag->all()]);
            $order->setStatus(ShopOrder::STATUS_PENDING);
        }
    }

    private function setFailedTransactionData(ShopOrder $order, ?ParameterBag $bag = null): void
    {
        $order->setStatus(ShopOrder::STATUS_FAILED);

        $user = $order->getUser();
        $user->setResetToken(null);
        $user->setResetRequestAt(null);

        if ($order->getPaymentType() === ShopOrder::PAYMENT_TYPE_CREDIT_CARD) {
            $order->setTransactionData([ShopOrder::CART_STATUS_REJECT => $bag->all()]);
        }
    }
}
