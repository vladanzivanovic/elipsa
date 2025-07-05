<?php

declare(strict_types=1);

namespace App\Parser\Site\Order;

use App\Entity\Address;
use App\Entity\ShopOrder;
use App\Exception\OrderException;
use App\Parser\Site\AddressParser;
use App\Repository\LocationRepository;
use Symfony\Component\HttpFoundation\ParameterBag;

final class OrderCompleteParser
{
    public function __construct(
        private readonly OrderRequestParser $orderRequestParser,
        private readonly AddressParser $addressParser,
        private readonly OrderUserParser $orderUserParser,
        private readonly LocationRepository $locationRepository,
    ) {}

    /**
     * @throws OrderException
     */
    public function parse(string $orderToken, ParameterBag $bag): ShopOrder
    {
        $order = $this->orderRequestParser->findOrder($orderToken);

        if ($order->getCompletedAt() instanceof \DateTimeInterface) {
            throw new OrderException('order.already_completed');
        }

        $shippingAddress = $this->setAddress($bag->get('shipping_address'));
        $billingAddress = $this->setAddress($bag->get('billing_address'));

        $order->setShippingAddress($shippingAddress);
        $order->setBillingAddress($billingAddress);

        $this->setUser($bag->get('user'), $order);

        $order->setPaymentType($bag->get('payment_type'));
        $order->setNote($bag->get('order_note'));
        $order->setStatus(ShopOrder::STATUS_PENDING);
        $order->setShippingType($bag->get('shipping_type'));
        $order->setVisited(false);

        $this->removeUnavailableOrderProducts($order);

        if (ShopOrder::PAYMENT_TYPE_CREDIT_CARD === $order->getPaymentType()) {
            $order->setCardStatus(ShopOrder::CARD_STATUS_PRE_AUTH);
        }

        if (ShopOrder::SHIPPING_TYPE_IN_STORE === $bag->get('shipping_type')) {
            $location = $this->locationRepository->find($bag->getInt('store_location'));

            $order->setStoreId($location);
        }

        return $order;
    }

    private function setAddress(array $addressData): Address
    {
        $bag = new ParameterBag($addressData);

        return $this->addressParser->parse($bag);
    }

    private function setUser(array $userData, ShopOrder $order): void
    {
        $bag = new ParameterBag($userData);

        $user = $this->orderUserParser->parse($bag);

        $order->setUser($user);
    }

    private function removeUnavailableOrderProducts(ShopOrder $order): void
    {
        foreach ($order->getOrderProducts() as $orderProduct) {
            if (false === $orderProduct->isProductAvailable()) {
                $order->removeOrderProduct($orderProduct);

                continue;
            }

            $productOption = $orderProduct->getProduct()->getOptionByCountry($order->getCountry());

            if (true === $productOption->isSold()) {
                $order->removeOrderProduct($orderProduct);
            }
        }
    }
}
