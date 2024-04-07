<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Banner;
use App\Entity\Product;
use App\Entity\Slider;
use App\Helper\ConstantsHelper;
use App\Model\DataTableModel;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class LocationDataTableResponseFormatter
{
    use DataTableResponseTrait;
    private \Symfony\Component\Routing\RouterInterface $router;

    /**
     * BannerDataTableResponseFormatter constructor.
     */
    public function __construct(
        RouterInterface $router
    ) {
        $this->router = $router;
    }

    
    public function formatResponse(DataTableModel $tableModel, array $data, int $total): array
    {
        return $this->response($tableModel, $data, $total);

    }
}