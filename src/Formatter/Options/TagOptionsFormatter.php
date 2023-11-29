<?php

declare(strict_types=1);

namespace App\Formatter\Options;

use App\Entity\Tags;
use App\Repository\TagsRepository;
use App\View\TagView;

final class TagOptionsFormatter
{
    private TagView $tagView;

    private TagsRepository $tagsRepository;

    private string $defaultLocale;

    public function __construct(
        TagView $tagView,
        TagsRepository $tagsRepository,
        string $defaultLocale
    ) {

        $this->tagView = $tagView;
        $this->defaultLocale = $defaultLocale;
        $this->tagsRepository = $tagsRepository;
    }

    public function formatTagOptions(int $type = Tags::TYPE_PRODUCT, $locale = null): array
    {
        $tags = $this->tagsRepository->getForOptions($type, $locale ?? $this->defaultLocale);

        $formattedTags = [];

        foreach ($tags as $tag) {
            $formattedTags[] = $this->tagView->getForOptions($tag, $locale ?? $this->defaultLocale);
        }

        return $formattedTags;
    }
}
