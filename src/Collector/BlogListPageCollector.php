<?php

declare(strict_types=1);

namespace App\Collector;

use App\Repository\BlogRepository;
use App\Services\PaginationService;

final class BlogListPageCollector
{
    public function __construct(
        private readonly PaginationService $paginationService,
        private readonly BlogRepository $blogRepository
    ) {}

    public function collect(string $locale, string $countryCode, int $currentPage, null|string $tag = null): array
    {
        $blogDql = $this->blogRepository->getDqlForPaginationPage($countryCode, $tag);
        return $this->paginationService->pagination($blogDql, $currentPage, 12);

    }
}
