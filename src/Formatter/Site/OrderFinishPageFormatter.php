<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Entity\OrderProduct;
use App\Entity\ShopOrder;
use App\Repository\SettingsRepository;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Routing\RouterInterface;

final class OrderFinishPageFormatter
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var SettingsRepository
     */
    private $settingsRepository;

    /**
     * CartPageFormatter constructor.
     *
     * @param RouterInterface    $router
     * @param SettingsRepository $settingsRepository
     */
    public function __construct(
        RouterInterface $router,
        SettingsRepository $settingsRepository
    ) {
        $this->router = $router;
        $this->settingsRepository = $settingsRepository;
    }

    public function formatResponse(array $data, string $locale, ParameterBag $parameterBag): array
    {
        /** @var ShopOrder $order */
        $order = $data['order'];
        $productArray = [];
        $total = 0;

        $promoCode = $order->getCoupon();

        /** @var OrderProduct $orderProduct */
        foreach ($order->getOrderProducts()->getIterator() as $orderProduct) {
            $trans = $orderProduct->getByLocale($locale);

            $productArray[] = [
                'id'        => $orderProduct->getId(),
                'name'      => $trans->getTitle(),
                'slug'      => $trans->getSlug(),
                'price'     => $orderProduct->getPrice(),
                'discount'  => $orderProduct->getDiscount(),
                'quantity'  => $orderProduct->getQuantity(),
            ];

            $total += $orderProduct->getDiscount() > 0 ? $orderProduct->getDiscount()*$orderProduct->getQuantity() : $orderProduct->getPrice()*$orderProduct->getQuantity();
        }

        if ($order->getPaymentType() === ShopOrder::PAYMENT_TYPE_CREDIT_CARD) {
            $data['transaction_date_time'] = new \DateTime($parameterBag->get('EXTRA_TRXDATE'));
            $data['transaction_id'] = $parameterBag->get('TransId');
            $data['auth_code'] = $parameterBag->get('AuthCode');
            $data['payment_response'] = $parameterBag->get('Response');
            $data['proc_return_code'] = $parameterBag->get('ProcReturnCode');
            $data['md_status'] = $parameterBag->get('mdStatus');
        }

        return array_merge($data, [
            'products' => $productArray,
            'total' => $total,
            'shipping' => $total >= $data['settings']['FREE_SHIPPING'] ? 0 : $data['settings']['SHIPPING_PRICE'],
            'free_shipping_price' => $data['settings']['FREE_SHIPPING'],
            'shipping_price' => $data['settings']['SHIPPING_PRICE'],
            'promo_price' => null !== $promoCode ? $promoCode->getDiscount() : null,
        ]);
    }
}