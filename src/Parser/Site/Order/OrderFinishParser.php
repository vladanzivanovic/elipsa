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
    public function parse(string $orderToken, bool $isSuccessfulTransaction): ShopOrder
    {
        $order = $this->orderRequestParser->findOrder($orderToken);

        if ($order->getCompletedAt() instanceof \DateTimeInterface) {
            throw new OrderException('order.already_completed');
        }

        if ($isSuccessfulTransaction) {
            $this->setSuccessfulTransactionData($order);
        } else {
            $this->setFailedTransactionData($order);
        }

        return $order;
    }

    private function setSuccessfulTransactionData(ShopOrder $order): void
    {
        $order->setStatus(ShopOrder::STATUS_PENDING);
        $order->setCompletedAt(new \DateTime());
    }

    private function setFailedTransactionData(ShopOrder $order): void
    {
        $order->setStatus(ShopOrder::STATUS_FAILED);

        $user = $order->getUser();
        $user->setResetToken(null);
        $user->setResetRequestAt(null);
    }
}
