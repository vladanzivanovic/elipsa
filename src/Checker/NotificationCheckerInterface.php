<?php

declare(strict_types=1);

namespace App\Checker;

use App\Request\Dto\NotificationRequestDto;

interface NotificationCheckerInterface
{
    public function isNotifyEligible(NotificationRequestDto $notificationRequestDto): bool;

    public function getType(): string;
}
