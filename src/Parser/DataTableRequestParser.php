<?php

declare(strict_types=1);

namespace App\Parser;

use App\Model\DataTableModel;
use Symfony\Component\HttpFoundation\Request;

final class DataTableRequestParser
{
    public function formatRequest(Request $request): DataTableModel
    {
        $bag = $request->request;
        $columns = $bag->all('columns');
        $order = $bag->all('order');

        return new DataTableModel(
            $bag->getInt('draw'),
            $bag->getInt('start'),
            $bag->getInt('length'),
            $columns,
            !empty($order) ? (int)$order[0]['column'] : 0,
            !empty($order) ? $order[0]['dir'] : 'desc',
            $bag->all('search')['value']
        );
    }
}
