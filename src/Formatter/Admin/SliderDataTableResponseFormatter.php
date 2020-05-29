<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Product;
use App\Entity\Slider;
use App\Helper\ConstantsHelper;
use App\Model\DataTableModel;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SliderDataTableResponseFormatter
{
    use DataTableResponseTrait;
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * SliderDataTableResponseFormatter constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(
        RouterInterface $router
    ) {
        $this->router = $router;
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
        $router = $this->router;

        $data = array_map(function ($slider) use ($router) {
            $statusText = ConstantsHelper::getConstantName((string)$slider['is_active'], 'STATUS', Slider::class);
            $slider['status_text'] = $statusText;

            $image = $router->generate('app.image_show', ['entity' => 'slider', 'name' => $slider['name'], 'filter' => "admin_slider_list"]);
            $slider['image'] = $image;

            return $slider;
        }, $data);

        return $this->response($tableModel, $data, $total);

    }
}