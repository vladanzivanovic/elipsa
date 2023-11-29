<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Model\DataTableModel;

final class PromotionCouponsDataTableResponseFormatter
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
        $data = array_map(function ($coupon) {
            $coupon['validFrom'] = $coupon['validFrom']->format('d.m.Y');
            $coupon['validTo'] = $coupon['validTo']->format('d.m.Y');

            return $coupon;
        }, $data);

        return $this->response($tableModel, $data, $total);

    }
}
