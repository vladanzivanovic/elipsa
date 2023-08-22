<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Settings;

final class SettingsView
{
    public function view(Settings $settings): array
    {
        return [
            'name' => $settings->getName(),
            'slug' => $settings->getSlug(),
            'value' => $settings->getValue(),
            'locale' => $settings->getLocale(),
            'input_type' => $settings->getInputType(),
        ];
    }
}
