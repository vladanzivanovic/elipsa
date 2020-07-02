<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\ShopOrder;
use App\Helper\ConstantsHelper;
use App\Model\DataTableModel;

final class OrderDataTableResponseFormatter
{
    use DataTableResponseTrait;

    /**
     * @param DataTableModel $tableModel
     * @param array          $data
     * @param int            $total
     *
     * @return array
     */
    public function formatResponse(DataTableModel $tableModel, array $data, int $total): array
    {
        $data = array_map(function ($order) {
            $order['payment_type'] = 'payment_type.'.ConstantsHelper::getConstantName((string) $order['payment_type'], 'PAYMENT_TYPE', ShopOrder::class);

            return $order;
        }, $data);
        return $this->response($tableModel, $data, $total);

    }
}