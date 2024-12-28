<?php

declare(strict_types=1);

namespace App\Formatter\Options;

use App\Repository\ProductColorRepository;

final class ColorsOptionsFormatter
{
    public function __construct(
        private readonly ProductColorRepository $productColorRepository,
    ) {}

    public function format(): array
    {
        return $this->productColorRepository->getForOptions();
    }
}
