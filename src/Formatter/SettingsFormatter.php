<?php

declare(strict_types=1);

namespace App\Formatter;

use App\Entity\Settings;

final class SettingsFormatter
{
    /**
     * @param array<int, Settings> $settings
     *
     * @return array<string, mixed>
     */
    public function formatResponse(array $settings): array
    {
        $formatted = [];

        foreach ($settings as $setting) {
            $name = strtolower($setting->getSlug());
            $formatted[$name] = $setting;
        }

        return $formatted;
    }
}
