<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\View\TagView;

final class ShopFilterFormatter
{
    private TagView $tagView;

    public function __construct(
        TagView $tagView
    ) {
        $this->tagView = $tagView;
    }

    public function format(array $filters): array
    {
        $formattedData = [
            'collections' => $this->formatTags($filters['collection']),
            'seasons' => $this->formatTags($filters['season']),
            'attributes' => $this->formatTags($filters['attributes']),
            'sizes' => $filters['sizes'],
            'colors' => $filters['colors'],
            'price' => $filters['price'],
        ];

        return $formattedData;
    }

    private function formatTags(array $tags): array
    {
        $formattedTags = [];

        foreach ($tags as $tag) {
            $formattedTags[] = $this->tagView->view($tag);
        }

        return $formattedTags;
    }
}
