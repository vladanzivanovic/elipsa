<?php

declare(strict_types=1);

namespace App\Twig;

use App\Repository\OfficeContactRepository;
use App\View\OfficeContactView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class OfficeContactExtension extends AbstractExtension
{
    private OfficeContactRepository $officeContactRepository;

    private OfficeContactView $officeContactView;

    public function __construct(
        OfficeContactRepository $officeContactRepository,
        OfficeContactView $officeContactView
    ) {
        $this->officeContactRepository = $officeContactRepository;
        $this->officeContactView = $officeContactView;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('office_contacts', [$this, 'getOfficeContact']),
        ];
    }

    public function getOfficeContact(string $countryCode): array
    {
        $contacts = $this->officeContactRepository->getFooterContacts($countryCode);

        $formatted = [];

        foreach ($contacts as $contact) {
            $formatted[] = $this->officeContactView->siteView($contact);
        }

        return $formatted;
    }

    public function getName(): string
    {
        return 'settings_extension';
    }
}
