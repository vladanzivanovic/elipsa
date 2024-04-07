<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Banner;
use App\Entity\Collaborator;
use App\Entity\Product;
use App\Entity\Slider;
use App\Helper\ConstantsHelper;
use App\Model\DataTableModel;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CareerDataTableResponseFormatter
{
    use DataTableResponseTrait;
    private \Symfony\Component\Routing\RouterInterface $router;
    private \Symfony\Contracts\Translation\TranslatorInterface $translator;

    public function __construct(
        RouterInterface $router,
        TranslatorInterface $translator
    ) {
        $this->router = $router;
        $this->translator = $translator;
    }

    
    public function formatResponse(DataTableModel $tableModel, array $data, int $total): array
    {
        return $this->response($tableModel, $data, $total);

    }
}