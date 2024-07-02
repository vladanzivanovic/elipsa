<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\ShopOrder;
use App\Helper\ConstantsHelper;
use App\Model\DataTableModel;
use Symfony\Contracts\Translation\TranslatorInterface;
use function GuzzleHttp\Psr7\str;

final class OrderDataTableResponseFormatter
{
    use DataTableResponseTrait;

    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly array $countries,
    ) {}

    
    public function formatResponse(DataTableModel $tableModel, array $data, int $total): array
    {
        $data = array_map(function ($order) {
            $order['status'] = 'order.status.'.ConstantsHelper::getConstantName((string) $order['status'], 'STATUS', ShopOrder::class);

            $order['payment_type'] = 'payment_type.'.ConstantsHelper::getConstantName((string) $order['payment_type'], 'PAYMENT_TYPE', ShopOrder::class);

            foreach ($this->countries as $countryCode => $country) {
                if ($order['country_code'] !== $countryCode) {
                    continue;
                }

                $order['country'] = $this->translator->trans($country['translation']);
            }

            return $order;
        }, $data);

        return $this->response($tableModel, $data, $total);
    }
}
