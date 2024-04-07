<?php

declare(strict_types=1);

namespace App\Collector\Admin;

use App\Repository\SettingsRepository;

final class SettingsPageCollector
{
    private SettingsRepository $settingsRepository;

    public function __construct(
        SettingsRepository $settingsRepository
    ) {
        $this->settingsRepository = $settingsRepository;
    }

    public function collect(): array
    {
        $companySettings = $this->settingsRepository->findBy(['slug' => [
            'FULL_COMPANY_NAME', 'TELEPHONE', 'STREET', 'CITY', 'ZIP_CODE', 'ACCOUNT_NUMBER', 'PIB', 'COMPANY_ACTIVITY', 'COMPANY_CODE', 'COMPANY_ID'
        ]]);

        $siteSettings = $this->settingsRepository->findBy(['slug' => [
            'SITE_NAME', 'MAIN_EMAIL', 'SHIPPING_PRICE', 'FREE_SHIPPING', 'FREE_SHIPPING_STORE'
        ]]);

        $footerText = $this->settingsRepository->findBy(['slug' => 'FOOTER_BOTTOM_TEXT']);

        return [
            'settings' => [
                'company' => $companySettings,
                'site' => $siteSettings,
                'footer' => $footerText,
            ]
        ];
    }
}
