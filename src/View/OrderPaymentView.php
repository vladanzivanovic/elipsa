<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\ShopOrder;

final class OrderPaymentView
{
    public function view(ShopOrder $order): array
    {
        if (empty($order->getTransactionData())) {
            return [];
        }

        $transactionData = $order->getTransactionData();
        $preAuth = $transactionData['PreAuth'];

        dd($transactionData);

        $view = [
            'pre_auth' => [
                'transaction_date_time' => $preAuth['EXTRA_TRXDATE'] ? new \DateTime($preAuth['EXTRA_TRXDATE']) : null,
                'transaction_id' => $preAuth['TransId'],
                'auth_code' => $preAuth['AuthCode'],
                'payment_response' => $preAuth['Response'],
                'proc_return_code' => $preAuth['ProcReturnCode'],
                'md_status' => $preAuth['mdStatus'],
            ]
        ];

        return $view;
    }
}
