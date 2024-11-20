<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\ShopOrder;
use App\Helper\ConstantsHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OrderPaymentView
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ){}

    /**
     * @throws \ReflectionException
     */
    public function view(ShopOrder $order): array
    {
        if ($order->getTransactionData() === null || $order->getTransactionData() === []) {
            return $this->defaultValues($order);
        }

        $transactionData = $order->getTransactionData();

        $methodName = 'set'.ucfirst($order->getCountry()).'PaymentData';

        $view = $this->$methodName($transactionData);

        return $this->defaultValues($order) + $view;
    }

    /**
     * @throws \ReflectionException
     */
    private function defaultValues(ShopOrder $order): array
    {
        $paymentType = ConstantsHelper::getConstantName($order->getPaymentType(), 'PAYMENT_TYPE', ShopOrder::class);
        return [
            'type' => $order->getPaymentType(),
            'human_type' => $this->translator->trans('payment_type.' . $paymentType),
            'status' => $order->getCardStatus(),
        ];
    }

    private function setRsPaymentData(array $transactionData): array
    {
        if (isset($transactionData[ShopOrder::CARD_STATUS_FAILED])) {
            $cardData = $transactionData[ShopOrder::CARD_STATUS_FAILED];

            return [
                strtolower(ShopOrder::CARD_STATUS_FAILED) => [
                    'transaction_date_time' => new \DateTime(),
                    'transaction_id' => null,
                    'auth_code' => null,
                    'payment_response' => null,
                    'proc_return_code' => null,
                    'md_status' => null,
                ]
            ];
        }

        $cardData = $transactionData[ShopOrder::CARD_STATUS_PRE_AUTH];

         return [
            strtolower(ShopOrder::CARD_STATUS_PRE_AUTH) => [
                'transaction_date_time' => $cardData['EXTRA_TRXDATE'] ? new \DateTime($cardData['EXTRA_TRXDATE']) : null,
                'transaction_id' => $cardData['TransId'],
                'auth_code' => $cardData['AuthCode'],
                'payment_response' => $cardData['Response'],
                'proc_return_code' => $cardData['ProcReturnCode'],
                'md_status' => $cardData['mdStatus'],
            ]
        ];
    }

    private function setBaPaymentData(array $transactionData): array
    {
        if (isset($transactionData[ShopOrder::CARD_STATUS_REJECTED])) {
            $cardData = $transactionData[ShopOrder::CARD_STATUS_REJECTED];

            return [
                strtolower(ShopOrder::CARD_STATUS_REJECTED) => [
                    'transaction_date_time' => new \DateTime(),
                    'transaction_id' => $cardData['merchantTransactionId'],
                    'auth_code' => $cardData['purchaseId'],
                    'payment_response' => $cardData['result'],
                    'proc_return_code' => $cardData['ProcReturnCode'],
                    'md_status' => $cardData['returnData']['eci'],
                ]
            ];
        }

        if (isset($transactionData[ShopOrder::CARD_STATUS_PRE_AUTH])) {
            $cardData = $transactionData[ShopOrder::CARD_STATUS_PRE_AUTH];

            return [
                strtolower(ShopOrder::CARD_STATUS_PRE_AUTH) => [
                    'transaction_date_time' => new \DateTime(),
                    'transaction_id' => $cardData['merchantTransactionId'],
                    'auth_code' => $cardData['purchaseId'],
                    'payment_response' => $cardData['result'],
                    'proc_return_code' => null,
                    'md_status' => $cardData['returnData']['eci'],
                ]
            ];
        }

        return [];
    }
}
