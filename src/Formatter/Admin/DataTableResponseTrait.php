<?php

namespace App\Formatter\Admin;

use App\Model\DataTableModel;

trait DataTableResponseTrait
{
    
    public function response(DataTableModel $tableModel, array $data, int $total): array
    {
        return [
            'draw' => $tableModel->getDraw(),
            'data' => $data,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
        ];
    }
}