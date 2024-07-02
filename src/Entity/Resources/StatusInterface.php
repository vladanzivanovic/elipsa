<?php

declare(strict_types=1);

namespace App\Entity\Resources;

interface StatusInterface
{
    const STATUS_ACTIVE = 'active';

    const STATUS_PENDING = 'pending';

    const STATUS_ARCHIVED = 'archived';

    public function getStatus(): ?string;

    public function setStatus(string $status): void;
}
