<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Banner;
use App\Entity\Location;
use App\Entity\OrderProduct;
use App\Entity\PromotionCoupon;
use App\Entity\ShopOrder;
use App\Repository\ImageRepository;
use App\Repository\SettingsRepository;
use Symfony\Component\Routing\RouterInterface;

final class OrderSingleResponseFormatter
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ImageRepository
     */
    private $imageRepository;
    /**
     * @var SettingsRepository
     */
    private $settingsRepository;

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
        /** @var PromotionCoupon $promoCode */
        $promoCode = $order->getCoupon();

        $freeShippingPrice = $this->settingsRepository->findOneBy(['slug' => 'FREE_SHIPPING']);
        $shippingPrice = $this->settingsRepository->findOneBy(['slug' => 'SHIPPING_PRICE']);
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

        $total = $freeShippingPrice->getValue() <= $productPrices ? $productPrices + $shippingPrice->getValue() : $productPrices;
        $priceWithDiscount = null;

        if (null !== $promoCode) {
            $priceWithDiscount = $productPrices - ($productPrices * ($promoCode->getDiscount() / 100));
            $total = $freeShippingPrice->getValue() <= $priceWithDiscount ? $priceWithDiscount + $shippingPrice->getValue() : $priceWithDiscount;
        }
        return [
            'id' => $order->getId(),
            'products' => $products,
            'productPrices' => $productPrices,
            'promoCode' => $promoCode,
            'priceWithDiscount' => $priceWithDiscount,
            'shippingPrice' => $shippingPrice->getValue(),
            'total' => $total,
            'orderDate' => null !== $order->getCompletedAt() ? $order->getCompletedAt()->format('d.m.Y') : null,
            'fullName' => $address->getFirstName().' '.$address->getLastName(),
            'email' => $address->getEmail(),
            'phone' => $address->getPhone(),
            'address' => $address->getAddress(),
            'zipCode' => $address->getZipCode(),
            'city' => $address->getCity(),
            'country' => $address->getCountry(),
        ];
    }
}