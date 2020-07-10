<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Banner;
use App\Entity\Description;
use App\Entity\Product;
use App\Entity\Slider;
use App\Helper\ConstantsHelper;
use App\Model\DataTableModel;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class DescriptionDataTableResponseFormatter
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
        $data = array_map(function ($desc) {
            $desc['type_text'] = ConstantsHelper::getConstantName((string) $desc['type'], 'TYPE', Description::class);

            return $desc;
        }, $data);

        return $this->response($tableModel, $data, $total);

    }
}