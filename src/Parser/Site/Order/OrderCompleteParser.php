<?php

declare(strict_types=1);

namespace App\Parser\Site\Order;

use App\Entity\Address;
use App\Entity\ShopOrder;
use App\Entity\User;
use App\Exception\OrderException;
use App\Parser\Site\AddressParser;
use App\Repository\LocationRepository;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

final class OrderCompleteParser
{
    private OrderRequestParser $orderRequestParser;

    private AddressParser $addressParser;

    private OrderUserParser $orderUserParser;

    private LocationRepository $locationRepository;

    public function __construct(
        OrderRequestParser $orderRequestParser,
        AddressParser $addressParser,
        OrderUserParser $orderUserParser,
        LocationRepository $locationRepository
    ) {
        $this->orderRequestParser = $orderRequestParser;
        $this->addressParser = $addressParser;
        $this->orderUserParser = $orderUserParser;
        $this->locationRepository = $locationRepository;
    }

    /**
     * @throws OrderException
     */
    public function parse(string $orderToken, ParameterBag $bag): ShopOrder
    {
        $order = $this->orderRequestParser->findOrder($orderToken);

        if (null !== $order->getCompletedAt()) {
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
}
