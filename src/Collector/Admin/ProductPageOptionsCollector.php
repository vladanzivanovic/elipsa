<?php

declare(strict_types=1);

namespace App\Collector\Admin;

use App\Formatter\Options\CategoryOptionsFormatter;
use App\Formatter\Options\TagOptionsFormatter;

final class ProductPageOptionsCollector
{
    private CategoryOptionsFormatter $categoryOptionsFormatter;

    private TagOptionsFormatter $tagOptionsFormatter;

    private string $defaultLocale;

    public function __construct(
        CategoryOptionsFormatter $categoryOptionsFormatter,
        TagOptionsFormatter $tagOptionsFormatter,
        string $defaultLocale
    ) {
        $this->categoryOptionsFormatter = $categoryOptionsFormatter;
        $this->tagOptionsFormatter = $tagOptionsFormatter;
        $this->defaultLocale = $defaultLocale;
    }

    public function options(): array
    {
        $tags = array_map(function ($tag) {
            $tag['value'] = $tag['slug'];

            return $tag;
        }, $this->tagOptionsFormatter->formatTagOptions());

        return [
            'categories' => $this->categoryOptionsFormatter->format($this->defaultLocale),
            'tags' => $tags,
        ];
    }
}
