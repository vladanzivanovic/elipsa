<?php

declare(strict_types=1);

namespace App\Collector;

use App\Entity\Settings;
use App\Formatter\SettingsFormatter;
use App\Repository\SettingsRepository;

final class SettingsCollector
{
    private SettingsRepository $settingsRepository;

    private SettingsFormatter $settingsFormatter;

    public function __construct(
        SettingsRepository $settingsRepository,
        SettingsFormatter $settingsFormatter
    ) {
        $this->settingsRepository = $settingsRepository;
        $this->settingsFormatter = $settingsFormatter;
    }

    /**
     * @return array<int, Settings>
     */
    public function collect(string $type): array
    {
        $fields = $this->mapTypeToFields($type);

        $settings = $this->settingsRepository->findBy(['slug' => $fields]);

        return $this->settingsFormatter->formatResponse($settings);
    }

    private function mapTypeToFields(string $type): array
    {
        $fields = [];

        switch ($type) {
            case 'email':
                $fields = ['MAIN_EMAIL', 'SITE_NAME', 'PIB', 'ACCOUNT_NUMBER', 'TELEPHONE', 'STREET', 'ZIP_CODE', 'CITY'];
                break;
            case 'contactPage':
                $fields = ['MAIN_EMAIL', 'TELEPHONE', 'STREET', 'CITY', 'ZIP_CODE', 'ACCOUNT_NUMBER', 'PIB', 'SHIPPING_PRICE', 'FREE_SHIPPING', 'SITE_NAME', 'FULL_COMPANY_NAME', 'COMPANY_ACTIVITY', 'COMPANY_CODE', 'COMPANY_ID', 'FOOTER_BOTTOM_TEXT'];
                break;
        }

        return $fields;
    }
}
