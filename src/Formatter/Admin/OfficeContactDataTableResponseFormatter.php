<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Model\DataTableModel;

final class OfficeContactDataTableResponseFormatter
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
