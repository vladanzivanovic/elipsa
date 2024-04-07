<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Banner;
use App\Entity\Catalogue;
use App\Entity\Product;
use App\Entity\Slider;
use App\Helper\ConstantsHelper;
use App\Model\DataTableModel;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CatalogDataTableResponseFormatter
{
    use DataTableResponseTrait;

    
    public function formatResponse(DataTableModel $tableModel, array $data, int $total): array
    {
        $data = array_map(function ($catalog) {
            $statusText = ConstantsHelper::getConstantName((string) $catalog['status'], 'STATUS', Catalogue::class);
            $catalog['status_text'] = $statusText;

            return $catalog;
        }, $data);

        return $this->response($tableModel, $data, $total);

    }
}