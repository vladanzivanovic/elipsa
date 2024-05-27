<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Resources\StatusInterface;
use App\Helper\ConstantsHelper;
use App\Model\DataTableModel;

final class SliderTextDataTableResponseFormatter
{
    use DataTableResponseTrait;

    public function __construct(
        private readonly array $countries,
    ) {}

    /**
     *
     * @throws \ReflectionException
     */
    public function formatResponse(DataTableModel $tableModel, array $data, int $total): array
    {
        $data = array_map(function ($slider) {
            $slider['status_text'] = ConstantsHelper::getConstantName((string)$slider['status'], 'STATUS', StatusInterface::class);

            $hosts = [];

            foreach ($this->countries as $countryCode => $country) {
                foreach ($slider['available_countries'] as $availableCountryCode) {
                    if ($availableCountryCode === $countryCode) {
                        $hosts[$countryCode] = $country['host'];
                    }
                }
            }

            $slider['hosts'] = implode('<br>', $hosts);

            return $slider;
        }, $data);

        return $this->response($tableModel, $data, $total);

    }
}
