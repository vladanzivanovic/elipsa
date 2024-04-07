<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\OfficeContact;
use App\View\OfficeContactView;

final class OfficeContactEditResponseFormatter
{
    private OfficeContactView $OfficeContactView;

    public function __construct(
        OfficeContactView $OfficeContactView
    ) {
        $this->OfficeContactView = $OfficeContactView;
    }

    public function formatResponse(OfficeContact $officeContact = null): array
    {
        $response = [];

        if ($officeContact instanceof \App\Entity\OfficeContact) {
            $response['payload'] = $this->OfficeContactView->siteView($officeContact);
        }

        return $response;
    }
}
