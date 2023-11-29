<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Notification;

final class NotificationView
{
    public function view(Notification $notification): array
    {
        return [
            'payload' => $notification->getPayload(),
            'email_address' => $notification->getEmail(),
            'locale' => $notification->getLocale(),
            'type' => $notification->getType(),
        ];
    }
}
