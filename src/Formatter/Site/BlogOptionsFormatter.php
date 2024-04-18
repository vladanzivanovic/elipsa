<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\View\TagView;

final class BlogOptionsFormatter
{
    public function __construct(
        private readonly TagView $tagView
    ) {}

    public function format(array $options): array
    {
        return [
            'tags' => $this->formatTags($options['tags']),
        ];
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
