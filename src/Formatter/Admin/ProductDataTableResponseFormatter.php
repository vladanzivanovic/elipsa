<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Product;
use App\Helper\ConstantsHelper;
use App\Model\DataTableModel;
use App\Repository\ProductSizeRepository;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProductDataTableResponseFormatter
{
    use DataTableResponseTrait;

    private TranslatorInterface $translator;

    private ProductSizeRepository $sizeRepository;

    private RouterInterface $router;

    public function __construct(
        TranslatorInterface $translator,
        ProductSizeRepository $sizeRepository,
        RouterInterface $router
    ) {
        $this->translator = $translator;
        $this->sizeRepository = $sizeRepository;
        $this->router = $router;
    }

    public function formatResponse(DataTableModel $tableModel, array $data, int $total): array
    {
        $data = array_map(function ($product) {
            $statusText = ConstantsHelper::getConstantName((string) $product['status'], 'STATUS', Product::class);
            $product['status_text'] = $this->translator->trans($statusText);
            $product['position_text'] = null;
            $product['link'] = $this->router->generate('site.product_page', ['slug' => $product['slug']]);

            if ($product['show_home_page'] > 0) {
                $product['position_text'] = ConstantsHelper::getConstantName((string)$product['show_home_page'], 'HOME_PAGE', Product::class);
            }

            return $product;
        }, $data);

        return $this->response($tableModel, $data, $total);

    }
}
