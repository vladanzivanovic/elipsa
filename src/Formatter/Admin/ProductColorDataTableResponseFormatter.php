<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Product;
use App\Helper\ConstantsHelper;
use App\Model\DataTableModel;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProductColorDataTableResponseFormatter
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
        return $this->response($tableModel, $data, $total);
    }
}