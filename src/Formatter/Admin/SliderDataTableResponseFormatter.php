<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Slider;
use App\Helper\ConstantsHelper;
use App\Model\DataTableModel;
use Symfony\Component\Routing\RouterInterface;

final class SliderDataTableResponseFormatter
{
    use DataTableResponseTrait;

    public function __construct(
        private readonly RouterInterface $router,
        private readonly array $countries,
    ) {}

    
    public function formatResponse(DataTableModel $tableModel, array $data, int $total): array
    {
        $router = $this->router;

        $data = array_map(function ($slider) use ($router) {
            $statusText = ConstantsHelper::getConstantName((string)$slider['is_active'], 'STATUS', Slider::class);
            $slider['status_text'] = $statusText;

            $image = $router->generate('app.image_show', ['entity' => 'slider', 'name' => $slider['name'], 'filter' => "admin_slider_list"]);
            $slider['image'] = $image;

            $hosts = [];

            foreach ($this->countries as $countryCode => $country) {
                foreach ($slider['available_countries'] as $availableCountryCode) {
                    if ($availableCountryCode === $countryCode) {
                        $hosts[$countryCode] = $country['host'];
                    }
                }
            }

            $slider['hosts'] = implode('<br>', $hosts);


            return $slider;
        }, $data);

        return $this->response($tableModel, $data, $total);

    }
}
