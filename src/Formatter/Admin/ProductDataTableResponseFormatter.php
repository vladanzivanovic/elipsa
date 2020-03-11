<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Product;
use App\Helper\ConstantsHelper;
use App\Model\DataTableModel;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProductDataTableResponseFormatter
{
    use DataTableResponseTrait;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * AdsDataTableResponseFormatter constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

    /**
     * @param DataTableModel $tableModel
     * @param array          $data
     * @param int            $total
     *
     * @return array
     */
    public function formatResponse(DataTableModel $tableModel, array $data, int $total): array
    {
        $data = array_map(function ($product) {
            $statusText = ConstantsHelper::getConstantName((string)$product['status'], 'STATUS', Product::class);
            $product['status_text'] = $this->translator->trans($statusText);

            return $product;
        }, $data);

        return $this->response($tableModel, $data, $total);

    }
}