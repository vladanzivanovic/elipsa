<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Collector\SettingsCollector;
use App\Entity\ShopOrder;
use App\Formatter\SettingsFormatter;
use App\View\OrderFinishView;

final class OrderFinishPageFormatter
{
    private OrderFinishView $orderFinishView;

    private SettingsCollector $settingsCollector;

    private SettingsFormatter $settingsFormatter;

    public function __construct(
        OrderFinishView $orderFinishView,
        SettingsCollector $settingsCollector,
        SettingsFormatter $settingsFormatter
    ) {
        $this->orderFinishView = $orderFinishView;
        $this->settingsCollector = $settingsCollector;
        $this->settingsFormatter = $settingsFormatter;
    }

    public function formatResponse(
        ShopOrder $order,
        string $locale,
        bool $isSuccessfulTransaction
    ): array {
        $settings = $this->settingsCollector->collect('email');

        $view = $this->orderFinishView->view(
            $order,
            $settings,
            $locale,
            $isSuccessfulTransaction
        );

        $view['settings'] = $this->settingsFormatter->formatResponse($settings);

        return $view;
    }
}
