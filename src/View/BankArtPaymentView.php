<?php

declare(strict_types=1);

namespace App\View;

use Ixopay\Client\Transaction\Result;

final class BankArtPaymentView
{

    public function view(Result $result): array
    {
        return ['redirect_url' => $result->getRedirectUrl()];
    }
}
