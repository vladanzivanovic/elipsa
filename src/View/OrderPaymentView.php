<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\ShopOrder;
use App\Helper\ConstantsHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OrderPaymentView
{
    private TranslatorInterface $translator;

    public function __construct(
        TranslatorInterface $translator
    ){
        $this->translator = $translator;
    }

    /**
     * @throws \ReflectionException
     */
    public function view(ShopOrder $order): array
    {
        if (empty($order->getTransactionData())) {
            return $this->defaultValues($order);
        }

        $view = [];

        $transactionData = $order->getTransactionData();


        if (isset($transactionData[ShopOrder::CART_TYPE_REJECT])) {
            $cardData = $transactionData[ShopOrder::CART_TYPE_REJECT];

            $view = [
                strtolower(ShopOrder::CART_TYPE_REJECT) => [
                    'transaction_date_time' => $cardData['EXTRA_TRXDATE'] ? new \DateTime($cardData['EXTRA_TRXDATE']) : null,
                    'transaction_id' => $cardData['TransId'],
                    'auth_code' => $cardData['AuthCode'],
                    'payment_response' => $cardData['Response'],
                    'proc_return_code' => $cardData['ProcReturnCode'],
                    'md_status' => $cardData['mdStatus'],
                ]
            ];
        }

        if (isset($transactionData[ShopOrder::CARD_TYPE_PRE_AUTH])) {
            $cardData = $transactionData[ShopOrder::CARD_TYPE_PRE_AUTH];

            $view = [
                strtolower(ShopOrder::CARD_TYPE_PRE_AUTH) => [
                    'transaction_date_time' => $cardData['EXTRA_TRXDATE'] ? new \DateTime($cardData['EXTRA_TRXDATE']) : null,
                    'transaction_id' => $cardData['TransId'],
                    'auth_code' => $cardData['AuthCode'],
                    'payment_response' => $cardData['Response'],
                    'proc_return_code' => $cardData['ProcReturnCode'],
                    'md_status' => $cardData['mdStatus'],
                ]
            ];
        }

        return $this->defaultValues($order) + $view;
    }

    /**
     * @param ShopOrder $order
     * @return array
     * @throws \ReflectionException
     */
    private function defaultValues(ShopOrder $order): array
    {
        $paymentType = ConstantsHelper::getConstantName($order->getPaymentType(), 'PAYMENT_TYPE', ShopOrder::class);

        $view = [
            'type' => $order->getPaymentType(),
            'human_type' => $this->translator->trans('payment_type.' . $paymentType),
        ];
        return $view;
    }
}
