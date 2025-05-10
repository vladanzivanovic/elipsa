<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\PromotionOption;
use App\Model\DataTableModel;
use App\Repository\PromotionOptionRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PromotionCouponsDataTableResponseFormatter
{
    use DataTableResponseTrait;

    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly PromotionOptionRepository $promotionOptionRepository,
    ) {}

    
    public function formatResponse(DataTableModel $tableModel, array $data, int $total): array
    {
        $data = array_map(function ($coupon) {
            $coupon['validFrom'] = $coupon['validFrom']->format('d.m.Y');
            $coupon['validTo'] = $coupon['validTo']->format('d.m.Y');
            $coupon['type_text'] = $this->translator->trans('promotion.type.'.$coupon['type']);

            $option = $this->promotionOptionRepository->findOneBy(['promotionId' => $coupon['id'], 'type' => PromotionOption::OPTION_HOME_SCREEN_BAR]);

            $coupon['option_'.PromotionOption::OPTION_HOME_SCREEN_BAR] = $option !== null ? $option->getConfiguration()[0] : null;

            return $coupon;
        }, $data);

        return $this->response($tableModel, $data, $total);

    }
}
