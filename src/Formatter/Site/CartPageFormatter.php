<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Entity\ShopOrder;
use App\Formatter\Options\LocationOptionsFormatter;
use App\Repository\SettingsRepository;
use Symfony\Component\Routing\RouterInterface;

/**
 * @deprecated
 */
final class CartPageFormatter
{
    private RouterInterface $router;

    private SettingsRepository $settingsRepository;

    private LocationOptionsFormatter $locationOptionsFormatter;

    public function __construct(
        RouterInterface $router,
        SettingsRepository $settingsRepository,
        LocationOptionsFormatter $locationOptionsFormatter
    ) {
        $this->router = $router;
        $this->settingsRepository = $settingsRepository;
        $this->locationOptionsFormatter = $locationOptionsFormatter;
    }

    public function formatResponse(array $orderProducts, string $locale): array
    {
        /** @var ShopOrder $order */
        $order = $orderProducts['order'];
        $productArray = [];
        $total = 0;

        $freeShippingPrice = $this->settingsRepository->findOneBy(['slug' => 'FREE_SHIPPING']);
        $shippingPrice = $this->settingsRepository->findOneBy(['slug' => 'SHIPPING_PRICE']);
        $promoCode = $order->getCoupon();

        foreach ($orderProducts['products'] as $orderProduct) {
            $productArray[] = [
                'id'        => $orderProduct['id'],
                'name'      => $orderProduct['title'],
                'slug'      => $orderProduct['slug'],
                'image_link' => $this->router->generate('app.image_show', ['entity' => 'product', 'name' => $orderProduct['image_name'], 'filter' => 'cart_thumb']),
                'price'     => $orderProduct['price'],
                'discount'  => $orderProduct['discount'],
                'quantity'  => $orderProduct['quantity'],
            ];

            $total += $orderProduct['discount'] > 0 ? $orderProduct['discount']*$orderProduct['quantity'] : $orderProduct['price']*$orderProduct['quantity'];
        }

        return [
            'products' => $productArray,
            'total' => $total,
            'shipping' => $total >= $freeShippingPrice->getValue() ? 0 : $shippingPrice->getValue(),
            'free_shipping_price' => $freeShippingPrice->getValue(),
            'shipping_price' => $shippingPrice->getValue(),
            'promo_price' => null !== $promoCode ? $promoCode->getDiscount() : null,
            'options' => [
                'locations' => $this->locationOptionsFormatter->format($locale),
            ]
        ];
    }
}
