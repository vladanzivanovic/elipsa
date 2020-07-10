<?php

declare(strict_types=1);

namespace App\Collector;

use App\Entity\Tags;
use App\Repository\BlogRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\TagsRepository;
use App\Services\PaginationService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Contracts\Translation\TranslatorInterface;

final class BlogListPageCollector
{
    /**
     * @var PaginationService
     */
    private $paginationService;

    /**
     * @var TagsRepository
     */
    private $tagsRepository;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * @var BlogRepository
     */
    private $blogRepository;

    /**
     * @param PaginationService      $paginationService
     * @param TagsRepository         $tagsRepository
     * @param TranslatorInterface    $translator
     * @param ParameterBagInterface  $bag
     * @param BlogRepository         $blogRepository
     */
    public function __construct(
        PaginationService $paginationService,
        TagsRepository $tagsRepository,
        TranslatorInterface $translator,
        ParameterBagInterface $bag,
        BlogRepository $blogRepository
    ) {
        $this->paginationService = $paginationService;
        $this->tagsRepository = $tagsRepository;
        $this->translator = $translator;
        $this->bag = $bag;
        $this->blogRepository = $blogRepository;
    }

    /**
     * @param string      $locale
     * @param int         $currentPage
     * @param string|null $tag
     *
     * @return array
     */
    public function collect(string $locale, int $currentPage, ?string $tag = null): array
    {
        $tagMainSlug = [['mainSlug' => null]];
        $localizedUrl = null;

        if (null !== $tag) {
            $tagMainSlug = $this->tagsRepository->getMainSlug($tag, $locale, Tags::TYPE_BLOG);

            $localizedUrl = $this->tagsRepository->getForLocalization($tag, $locale === 'rs' ? 'en' : 'rs');
        }

        $blogDql = $this->blogRepository->getDqlForPaginationPage($locale, $tagMainSlug[0]['mainSlug']);
        $blogList = $this->paginationService->pagination($blogDql, $currentPage, 12);

        $blogIds = array_column($blogList['data'], 'id');

        $tags = $this->tagsRepository->findBy(['relatedType' => Tags::TYPE_BLOG, 'locale' => $locale]);

        return [
            'blog_list'         => $blogList,
            'tags'              => $tags,
            'localized_url'     => $localizedUrl,
        ];
    }
}