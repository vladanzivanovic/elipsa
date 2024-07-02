<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\OfficeContact;
use App\View\OfficeContactView;

final class OfficeContactEditResponseFormatter
{
    public function __construct(
        private readonly OfficeContactView $officeContactView
    ) {}

    public function formatResponse(OfficeContact $officeContact = null): array
    {
        $response = [];

        if ($officeContact instanceof OfficeContact) {
            $response['payload'] = $this->officeContactView->siteView($officeContact);
        }

        return $response;
    }
}
