<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Banner;
use App\Entity\Product;
use App\Entity\Slider;
use App\Helper\ConstantsHelper;
use App\Model\DataTableModel;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PromotionCouponsDataTableResponseFormatter
{
    use DataTableResponseTrait;
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(
        RouterInterface $router
    ) {
        $this->router = $router;
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

            return $coupon;
        }, $data);

        return $this->response($tableModel, $data, $total);

    }
}