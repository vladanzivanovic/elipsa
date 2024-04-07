<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\ShopOrder;
use App\Helper\ConstantsHelper;
use App\Model\DataTableModel;
use function GuzzleHttp\Psr7\str;

final class OrderDataTableResponseFormatter
{
    use DataTableResponseTrait;

    
    public function formatResponse(DataTableModel $tableModel, array $data, int $total): array
    {
        $data = array_map(function ($order) {
            $order['status'] = 'order.status.'.ConstantsHelper::getConstantName((string) $order['status'], 'STATUS', ShopOrder::class);

            $order['payment_type'] = 'payment_type.'.ConstantsHelper::getConstantName((string) $order['payment_type'], 'PAYMENT_TYPE', ShopOrder::class);

            return $order;
        }, $data);

        return $this->response($tableModel, $data, $total);
    }
}
