<?php

declare(strict_types=1);

namespace App\Formatter;

use App\Entity\Settings;
use App\View\SettingsView;

final class SettingsFormatter
{
    public function __construct(
        private readonly SettingsView $settingsView,
    ) {}

    /**
     * @param array<int, mixed> $settings
     *
     * @return array<string, mixed>
     */
    public function formatResponse(array $settings): array
    {
        $formatted = [];

        foreach ($settings as $setting) {
            $name = strtolower($setting->getSlug());

            if (null !== $setting->getLocale()) {
                $formatted[$name][$setting->getLocale()] = $this->settingsView->view($setting);

                continue;
            }

            $formatted[$name] = $this->settingsView->view($setting);
        }

        return $formatted;
    }
}
