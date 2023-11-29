<?php

declare(strict_types=1);

namespace App\Formatter\Options;

use App\Repository\CategoryRepository;

final class CategoryOptionsFormatter
{
    private CategoryRepository $categoryRepository;

    public function __construct(
        CategoryRepository $categoryRepository
    ){
        $this->categoryRepository = $categoryRepository;
    }

    public function format(string $locale): array
    {
        $options = $this->categoryRepository->getAll(null, $locale);

        return $options;
    }
}
