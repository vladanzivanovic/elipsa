<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Banner;
use App\Entity\Location;
use App\Entity\OrderProduct;
use App\Entity\PromotionCoupon;
use App\Entity\ShopOrder;
use App\Helper\ConstantsHelper;
use App\Repository\ImageRepository;
use App\Repository\SettingsRepository;
use Symfony\Component\Routing\RouterInterface;

final class OrderSingleResponseFormatter
{
    private RouterInterface $router;

    private ImageRepository $imageRepository;

    private SettingsRepository $settingsRepository;

    /**
     * @param RouterInterface    $router
     * @param ImageRepository    $imageRepository
     * @param SettingsRepository $settingsRepository
     */
    public function __construct(
        RouterInterface $router,
        ImageRepository $imageRepository,
        SettingsRepository $settingsRepository
    ) {
        $this->router = $router;
        $this->imageRepository = $imageRepository;
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * @param ShopOrder $order
     *
     * @return array
     */
    public function formatResponse(ShopOrder $order): array
    {
        $productCollection = $order->getOrderProducts();
        $productPrices = 0;
        $products = [];
        $total = 0;
        $hasShippingPrice = false;

        /** @var PromotionCoupon $promoCode */
        $promoCode = $order->getCoupon();

        $freeShippingPrice = $this->settingsRepository->findOneBy(['slug' => 'FREE_SHIPPING'])->getValue();
        $shippingPrice = $this->settingsRepository->findOneBy(['slug' => 'SHIPPING_PRICE'])->getValue();
        $address = $order->getBillingAddress();

        /** @var OrderProduct $product */
        foreach ($productCollection->getIterator() as $product) {
            $price = $product->getDiscount() > 0 ? $product->getDiscount() : $product->getPrice();
            $priceWithQuantity = $product->getQuantity() * $price;

            $trans = $product->getByLocale('rs');

            $products[] = [
                'id' => $product->getProduct()->getId(),
                'name' => $trans->getTitle(),
                'quantity' => $product->getQuantity(),
                'price' => $price,
                'priceWithQuantity' => $priceWithQuantity
            ];

            $productPrices += $priceWithQuantity;
        }

        $total = $freeShippingPrice > $productPrices ? $productPrices + $shippingPrice : $productPrices;

        if ($freeShippingPrice > $productPrices) {
            $total = $productPrices + $shippingPrice;
            $hasShippingPrice = true;
        }
        $priceWithDiscount = null;

        if (null !== $promoCode) {
            $priceWithDiscount = $productPrices - ($productPrices * ($promoCode->getDiscount() / 100));
            $total = $freeShippingPrice > $priceWithDiscount ? $priceWithDiscount + $shippingPrice : $priceWithDiscount;

            if ($freeShippingPrice > $priceWithDiscount) {
                $total = $priceWithDiscount + $shippingPrice;
                $hasShippingPrice = true;
            }
        }

        $data = [
            'id' => $order->getId(),
            'products' => $products,
            'productPrices' => $productPrices,
            'promoCode' => $promoCode,
            'priceWithDiscount' => $priceWithDiscount,
            'shippingPrice' => true === $hasShippingPrice ? $shippingPrice : 0,
            'total' => $total,
            'orderDate' => null !== $order->getCompletedAt() ? $order->getCompletedAt()->format('d.m.Y') : null,
            'fullName' => $address->getFirstName().' '.$address->getLastName(),
            'email' => $address->getEmail(),
            'phone' => $address->getPhone(),
            'address' => $address->getAddress(),
            'zipCode' => $address->getZipCode(),
            'city' => $address->getCity(),
            'country' => $address->getCountry(),
            'status' => $order->getStatus(),
            'statusText' => 'order.status.'.ConstantsHelper::getConstantName((string) $order->getStatus(), 'STATUS', ShopOrder::class),
            'orderType' => $order->getPaymentType(),
            'orderTypeText' => 'order.type.'.ConstantsHelper::getConstantName((string) $order->getPaymentType(), 'PAYMENT_TYPE', ShopOrder::class),
        ];

        if ($order->getPaymentType() === ShopOrder::PAYMENT_TYPE_CREDIT_CARD) {
            $cardData = $order->getTransactionData();

            $data['preAuthorizedDate'] = new \DateTime($cardData[ShopOrder::CARD_TYPE_PRE_AUTH]['EXTRA_TRXDATE']);
            $data['transactionId'] = $cardData[ShopOrder::CARD_TYPE_PRE_AUTH]['TransId'];
            $data['cardNumber'] = $cardData[ShopOrder::CARD_TYPE_PRE_AUTH]['maskedCreditCard'];

            if (isset($cardData[ShopOrder::CARD_TYPE_POST_AUTH])) {
                $data['postAuthorizedDate'] = new \DateTime($cardData[ShopOrder::CARD_TYPE_POST_AUTH]['Extra']['TRXDATE']);
            }

            if (isset($cardData[ShopOrder::CARD_TYPE_REFUND])) {
                $data['refundedDate'] = new \DateTime($cardData[ShopOrder::CARD_TYPE_REFUND]['Extra']['TRXDATE']);
            }

            if (isset($cardData[ShopOrder::CARD_TYPE_VOID])) {
                $data['voidDate'] = new \DateTime($cardData[ShopOrder::CARD_TYPE_VOID]['Extra']['TRXDATE']);
            }
        }

        return $data;
    }
}