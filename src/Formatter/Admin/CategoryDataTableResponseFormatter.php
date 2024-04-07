<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Product;
use App\Helper\ConstantsHelper;
use App\Model\DataTableModel;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CategoryDataTableResponseFormatter
{
    use DataTableResponseTrait;

    
    public function formatResponse(DataTableModel $tableModel, array $data, int $total): array
    {
        $data = array_map(function ($item) {
            $locales = explode(',', $item['locales']);
            $titles = explode(',', $item['titles']);

            $locales = array_map(function ($locale) {
                return $locale.'_name';
            },$locales);

            $item += array_combine($locales, $titles);

            if (null === $item['slug']) {
                unset($item['slug']);
            }

            return $item;
        }, $data);

        return $this->response($tableModel, $data, $total);
    }
}
