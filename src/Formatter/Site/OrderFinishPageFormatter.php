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

    public function __construct(
        OrderFinishView $orderFinishView,
        SettingsCollector $settingsCollector
    ) {
        $this->orderFinishView = $orderFinishView;
        $this->settingsCollector = $settingsCollector;
    }

    public function formatResponse(
        ShopOrder $order,
        string $locale,
        bool $isSuccessfulTransaction
    ): array {
        $officeInfo = $this->settingsCollector->collect('email');

        $view = $this->orderFinishView->view(
            $order,
            $officeInfo,
            $locale,
            $isSuccessfulTransaction
        );

        return $view;
    }
}
