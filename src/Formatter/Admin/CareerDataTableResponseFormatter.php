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
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     */
    public function __construct(
        RouterInterface $router,
        TranslatorInterface $translator
    ) {
        $this->router = $router;
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
        $data = array_map(function ($item) {
            if (null !== $item['cv_doc']) {
                $item['cv_doc'] = $this->router->generate('app.download_doc', ['id' => $item['cv_doc']], RouterInterface::ABSOLUTE_URL);
            }

            return $item;
        }, $data);
        return $this->response($tableModel, $data, $total);

    }
}