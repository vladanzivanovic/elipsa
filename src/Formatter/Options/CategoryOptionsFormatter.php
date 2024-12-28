<?php

declare(strict_types=1);

namespace App\Formatter\Options;

use App\Repository\CategoryRepository;

final class CategoryOptionsFormatter
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository
    ){}

    public function format(string $locale): array
    {
        return $this->categoryRepository->getAll(null, $locale);
    }
}
