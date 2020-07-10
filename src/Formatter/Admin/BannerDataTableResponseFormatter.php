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

final class BannerDataTableResponseFormatter
{
    use DataTableResponseTrait;
    /**
     * @var RouterInterface
     */
    private $router;

    /**
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

        $data = array_map(function ($banner) use ($router) {
            $statusText = ConstantsHelper::getConstantName((string) $banner['is_active'], 'STATUS', Banner::class);
            $banner['status_text'] = $statusText;
            $banner['type'] = ConstantsHelper::getConstantName((string) $banner['type'], 'TYPE', Banner::class);


            $image = $router->generate('app.image_show', ['entity' => 'banner', 'name' => $banner['name'], 'filter' => "admin_slider_list"]);
            $banner['image'] = $image;

            return $banner;
        }, $data);

        return $this->response($tableModel, $data, $total);

    }
}