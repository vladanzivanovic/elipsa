<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\ShopOrder;

final class OrderPaymentView
{
    public function view(ShopOrder $order): array
    {
        $view = [];

        if (empty($order->getTransactionData())) {
            return $view;
        }

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

        return $view;
    }
}
