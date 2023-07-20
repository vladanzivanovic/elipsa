<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\View\TagView;

final class TagOptionsFormatter
{
    private TagView $tagView;

    private string $defaultLocale;

    public function __construct(
        TagView $tagView,
        string $defaultLocale
    ) {

        $this->tagView = $tagView;
        $this->defaultLocale = $defaultLocale;
    }

    public function formatTagOptions(array $tags): array
    {
        $formattedTags = [];

        foreach ($tags as $tag) {
            $formattedTags[] = $this->tagView->getForOptions($tag, $this->defaultLocale);
        }

        return $formattedTags;
    }
}
