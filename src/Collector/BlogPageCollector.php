<?php

declare(strict_types=1);

namespace App\Collector;

use App\Entity\Tags;
use App\Repository\BlogTranslationRepository;
use App\Repository\TagsRepository;
use App\Request\Dto\BlogPageRequestDto;

final class BlogPageCollector
{
    public function __construct(
        private readonly TagsRepository $tagsRepository,
        private readonly BlogTranslationRepository $blogTranslationRepository,
    ) {}

    public function collect(BlogPageRequestDto $blogPageRequestDto): array
    {

        $blogTranslation = $this->blogTranslationRepository->findOneBy(['alias' => $blogPageRequestDto->slug]);

        $blog = $blogTranslation->getBlog();

        $blogTags = $this->tagsRepository->getByBlog($blog, $blogPageRequestDto->locale);
        $tags = $this->tagsRepository->findBy(['relatedType' => Tags::TYPE_BLOG]);

        return [
            'trans' => $blogTranslation,
            'blog_tags' => $blogTags,
            'blog' => $blog,
            'tags' => $tags,
        ];
    }
}
