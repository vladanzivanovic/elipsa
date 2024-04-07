<?php

declare(strict_types=1);

namespace App\Twig;

use App\Collector\SettingsCollector;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class SettingsExtension extends AbstractExtension
{
    private SettingsCollector $settingsCollector;

    public function __construct(
        SettingsCollector $settingsCollector
    ) {
        $this->settingsCollector = $settingsCollector;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('site_settings', [$this, 'getSettings']),
        ];
    }

    public function getSettings(string $locale): array|null
    {
        return $this->settingsCollector->collect();
    }

    public function getName(): string
    {
        return 'settings_extension';
    }
}
