<?php

declare(strict_types=1);

namespace App\Collector;

use App\Entity\Settings;
use App\Formatter\SettingsFormatter;
use App\Repository\OfficeContactRepository;
use App\Repository\SettingsRepository;
use App\View\OfficeContactView;

final class SettingsCollector
{
    private SettingsRepository $settingsRepository;

    private SettingsFormatter $settingsFormatter;

    private OfficeContactRepository $officeContactRepository;

    private OfficeContactView $officeContactView;

    public function __construct(
        SettingsRepository $settingsRepository,
        SettingsFormatter $settingsFormatter,
        OfficeContactRepository $officeContactRepository,
        OfficeContactView $officeContactView
    ) {
        $this->settingsRepository = $settingsRepository;
        $this->settingsFormatter = $settingsFormatter;
        $this->officeContactRepository = $officeContactRepository;
        $this->officeContactView = $officeContactView;
    }

    /**
     * @return array<int, Settings>
     */
    public function collect(string $type = null): array
    {
        $fields = $this->mapTypeToFields($type);

        $settings = $this->settingsCollection($fields['settings']);

        $officeContacts = $this->officeContactCollection($fields['office']);

        return ['settings' => $settings, 'office_contacts' => $officeContacts];
    }

    private function mapTypeToFields(string $type = null): array
    {
        switch ($type) {
            case 'email':
                $settingsMapper = ['MAIN_EMAIL', 'SITE_NAME', 'PIB', 'ACCOUNT_NUMBER', 'STREET', 'ZIP_CODE', 'CITY'];
                $officeMapper = ['useInEmail' => true];
                break;
            case 'contactPage':
                $settingsMapper = ['MAIN_EMAIL', 'STREET', 'CITY', 'ZIP_CODE', 'ACCOUNT_NUMBER', 'PIB', 'SHIPPING_PRICE', 'FREE_SHIPPING', 'SITE_NAME', 'FULL_COMPANY_NAME', 'COMPANY_ACTIVITY', 'COMPANY_CODE', 'COMPANY_ID', 'FOOTER_BOTTOM_TEXT'];
                $officeMapper = ['showInFooter' => true, 'useInEmail' => true];
                break;
            default:
                $settingsMapper = ['MAIN_EMAIL', 'SITE_NAME', 'PIB', 'ACCOUNT_NUMBER', 'STREET', 'ZIP_CODE', 'CITY', 'FULL_COMPANY_NAME', 'COMPANY_ID', 'FOOTER_BOTTOM_TEXT'];
                $officeMapper = ['showInFooter' => true, 'useInEmail' => true];
        }

        return ['settings' => $settingsMapper, 'office' => $officeMapper];
    }

    private function officeContactCollection(array $fields): array
    {
        $contacts = $this->officeContactRepository->getContactsByFields($fields);

        $contactsView = [];

        foreach ($contacts as $contact) {
            $contactsView[] = $this->officeContactView->siteView($contact);
        }

        return $contactsView;
    }

    private function settingsCollection(array $fields): array
    {
        $fields = $this->settingsRepository->findBy(['slug' => $fields]);

        return $this->settingsFormatter->formatResponse($fields);
    }
}
