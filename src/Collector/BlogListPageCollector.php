<?php

declare(strict_types=1);

namespace App\Collector;

use App\Entity\Tags;
use App\Repository\BlogRepository;
use App\Repository\TagsRepository;
use App\Services\PaginationService;

final class BlogListPageCollector
{
    private PaginationService $paginationService;

    private TagsRepository $tagsRepository;

    private BlogRepository $blogRepository;

    public function __construct(
        PaginationService $paginationService,
        TagsRepository $tagsRepository,
        BlogRepository $blogRepository
    ) {
        $this->paginationService = $paginationService;
        $this->tagsRepository = $tagsRepository;
        $this->blogRepository = $blogRepository;
    }

    /**
     * @param string|null $tag
     *
     */
    public function collect(string $locale, int $currentPage, ?string $tag = null): array
    {
        $tagMainSlug = [['mainSlug' => null]];
        $localizedUrl = null;

//        if (null !== $tag) {
//            $tagMainSlug = $this->tagsRepository->getMainSlug($tag, $locale, Tags::TYPE_BLOG);
//
//            $localizedUrl = $this->tagsRepository->getForLocalization($tag, $locale === 'rs' ? 'en' : 'rs');
//        }

        $blogDql = $this->blogRepository->getDqlForPaginationPage($locale, $tag);
        return $this->paginationService->pagination($blogDql, $currentPage, 12);

        dd($blogList);

//        $tags = $this->tagsRepository->findBy(['relatedType' => Tags::TYPE_BLOG]);

        return [
            'blog_list'         => $blogList,
            'tags'              => $tags,
            'localized_url'     => $localizedUrl,
        ];
    }
}
