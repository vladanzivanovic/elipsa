<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Catalogue;
use App\Helper\ConstantsHelper;
use App\Model\DataTableModel;

final class CatalogDataTableResponseFormatter
{
    use DataTableResponseTrait;

    public function __construct(
        private readonly array $countries,
    ) {}
    
    public function formatResponse(DataTableModel $tableModel, array $data, int $total): array
    {
        $data = array_map(function ($catalog) {
            $statusText = ConstantsHelper::getConstantName((string) $catalog['status'], 'STATUS', Catalogue::class);
            $catalog['status_text'] = $statusText;

            $hosts = [];

            foreach ($this->countries as $countryCode => $country) {
                foreach ($catalog['available_countries'] as $availableCountryCode) {
                    if ($availableCountryCode === $countryCode) {
                        $hosts[$countryCode] = $country['host'];
                    }
                }
            }

            $catalog['hosts'] = implode('<br>', $hosts);

            return $catalog;
        }, $data);

        return $this->response($tableModel, $data, $total);

    }
}
