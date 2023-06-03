<?php

declare(strict_types=1);

namespace App\Twig;

use App\Collector\CartPageCollector;
use App\Entity\BannerTranslation;
use App\Entity\Image;
use App\Formatter\Site\CartPageFormatter;
use App\Repository\BannerRepository;
use App\Repository\ImageRepository;
use App\Repository\SettingsRepository;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class SettingsExtension extends AbstractExtension
{
    private SettingsRepository $settingsRepository;

    public function __construct(
        SettingsRepository $settingsRepository
    ) {
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('site_settings', [$this, 'getSettings']),
        ];
    }

    /**
     * @return array|null
     */
    public function getSettings(string $locale): ?array
    {
        $settings = $this->settingsRepository->getAllSettingsByLocale($locale);

        $formatted = [];

        foreach ($settings as $setting) {
            $formatted[$setting['slug']] = $setting['value'];
        }

        return $formatted;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'settings_extension';
    }
}
