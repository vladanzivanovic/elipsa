<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Banner;
use App\Entity\CareerDescription;
use App\Entity\Product;
use App\Entity\Slider;
use App\Helper\ConstantsHelper;
use App\Model\DataTableModel;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class JobsDataTableResponseFormatter
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
        $router = $this->router;

        $data = array_map(function ($job) use ($router) {
            $statusText = ConstantsHelper::getConstantName((string) $job['status'], 'STATUS', CareerDescription::class);
            $job['status_text'] = $statusText;

            $image = $router->generate('app.image_show', ['entity' => 'job', 'name' => $job['name'], 'filter' => "admin_slider_list"]);
            $job['image'] = $image;

            return $job;
        }, $data);

        return $this->response($tableModel, $data, $total);

    }
}