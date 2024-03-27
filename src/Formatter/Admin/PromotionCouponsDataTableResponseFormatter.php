<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Model\DataTableModel;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PromotionCouponsDataTableResponseFormatter
{
    use DataTableResponseTrait;

    private TranslatorInterface $translator;

    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

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
            $coupon['type_text'] = $this->translator->trans('promotion.type.'.$coupon['type']);

            return $coupon;
        }, $data);

        return $this->response($tableModel, $data, $total);

    }
}
