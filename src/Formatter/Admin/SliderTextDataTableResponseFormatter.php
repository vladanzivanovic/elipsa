<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\SliderText;
use App\Helper\ConstantsHelper;
use App\Model\DataTableModel;

final class SliderTextDataTableResponseFormatter
{
    use DataTableResponseTrait;

    /**
     *
     * @throws \ReflectionException
     */
    public function formatResponse(DataTableModel $tableModel, array $data, int $total): array
    {
        $data = array_map(function ($slider) {
            $slider['status_text'] = ConstantsHelper::getConstantName((string)$slider['is_active'], 'STATUS', SliderText::class);

            return $slider;
        }, $data);

        return $this->response($tableModel, $data, $total);

    }
}
