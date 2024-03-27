<?php

declare(strict_types=1);

namespace App\Entity;

interface StatusInterface
{
    const STATUS_ACTIVE = 'active';

    const STATUS_PENDING = 'pending';

    public function getStatus(): ?string;

    public function setStatus(string $status): void;
}
