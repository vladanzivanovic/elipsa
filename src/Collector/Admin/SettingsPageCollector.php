<?php

declare(strict_types=1);

namespace App\Collector\Admin;

use App\Repository\SettingsRepository;

final class SettingsPageCollector
{
    private SettingsRepository $settingsRepository;

    /**
     * @param SettingsRepository $settingsRepository
     */
    public function __construct(
        SettingsRepository $settingsRepository
    ) {
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * @return array
     */
    public function collect(): array
    {
        return [
            'settings' => $this->settingsRepository->findAll(),
        ];
    }
}
