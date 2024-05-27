<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\CareerDescription;
use App\Helper\ConstantsHelper;
use App\Model\DataTableModel;
use Symfony\Component\Routing\RouterInterface;

final class JobsDataTableResponseFormatter
{
    use DataTableResponseTrait;

    public function __construct(
        private readonly RouterInterface $router,
        private readonly array $countries,
    ) {}

    
    public function formatResponse(DataTableModel $tableModel, array $data, int $total): array
    {
        $router = $this->router;

        $data = array_map(function ($job) use ($router) {
            $statusText = ConstantsHelper::getConstantName((string) $job['status'], 'STATUS', CareerDescription::class);
            $job['status_text'] = $statusText;

            $image = $router->generate('app.image_show', ['entity' => 'job', 'name' => $job['name'], 'filter' => "admin_slider_list"]);
            $job['image'] = $image;

            $hosts = [];

            foreach ($this->countries as $countryCode => $country) {
                foreach ($job['available_countries'] as $availableCountryCode) {
                    if ($availableCountryCode === $countryCode) {
                        $hosts[$countryCode] = $country['host'];
                    }
                }
            }

            $job['hosts'] = implode('<br>', $hosts);

            return $job;
        }, $data);

        return $this->response($tableModel, $data, $total);

    }
}
