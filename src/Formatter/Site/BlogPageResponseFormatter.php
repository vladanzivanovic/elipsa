<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Formatter\Site\Router\BlogPageRouterFormatter;
use App\View\BlogView;

final class BlogPageResponseFormatter
{
    public function __construct(
        private readonly BlogView $blogView,
        private readonly BlogOptionsFormatter $blogOptionsFormatter,
        private readonly BlogPageRouterFormatter $blogPageRouterFormatter,
    ) {}

    /**
     * @param array<string, array<array<string|int, mixed>>> $data
     *
     *
     * @return array<string, array<array<string|int, mixed>>>
     */
    public function formatResponse(array $data, array $options, null|string $tag = null): array
    {
        $blogList = [];

        foreach ($data['data'] as $blog) {
            $blogList[] = $this->blogView->view($blog);
        }

        $localizedUrls = $this->blogPageRouterFormatter->createLocalizedLinks($tag);

        return [
            'blogs' => $blogList,
            'pagination' => $data['pagination'],
            'options' => $this->blogOptionsFormatter->format($options),
            '_web_links' => $localizedUrls,
        ];
    }
}
