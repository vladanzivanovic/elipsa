<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Banner;
use App\Helper\ConstantsHelper;
use App\Model\DataTableModel;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class BannerDataTableResponseFormatter
{
    use DataTableResponseTrait;

    public function __construct(
        private readonly RouterInterface $router,
        private readonly TranslatorInterface $translator,
        private readonly array $countries,
    ) {}

    
    public function formatResponse(DataTableModel $tableModel, array $data, int $total): array
    {
        $router = $this->router;

        $data = array_map(function ($banner) use ($router) {
            $statusText = ConstantsHelper::getConstantName((string) $banner['is_active'], 'STATUS', Banner::class);
            $banner['status_text'] = $statusText;
            $banner['type'] = $this->translator->trans('banner.'.ConstantsHelper::getConstantName((string) $banner['type'], 'TYPE', Banner::class));


            $image = $router->generate('app.image_show', ['entity' => 'banner', 'name' => $banner['name'], 'filter' => "admin_slider_list"]);
            $banner['image'] = $image;

            $hosts = [];

            foreach ($this->countries as $countryCode => $country) {
                foreach ($banner['available_countries'] as $availableCountryCode) {
                    if ($availableCountryCode === $countryCode) {
                        $hosts[$countryCode] = $country['host'];
                    }
                }
            }

            $banner['hosts'] = implode('<br>', $hosts);

            return $banner;
        }, $data);

        return $this->response($tableModel, $data, $total);

    }
}
