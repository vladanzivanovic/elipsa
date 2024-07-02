<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Model\DataTableModel;

final class OfficeContactDataTableResponseFormatter
{
    use DataTableResponseTrait;

    public function __construct(
        private readonly array $countries,
    ) {}

    public function formatResponse(DataTableModel $tableModel, array $data, int $total): array
    {
        $formattedData = array_map(function ($item) {
            $hosts = [];

            foreach ($this->countries as $countryCode => $country) {
                foreach ($item['available_countries'] as $availableCountryCode) {
                    if ($availableCountryCode === $countryCode) {
                        $hosts[$countryCode] = $country['host'];
                    }
                }
            }

            $item['hosts'] = implode('<br>', $hosts);

            return $item;
        }, $data);

        return $this->response($tableModel, $formattedData, $total);

    }
}
