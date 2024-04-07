<?php

declare(strict_types=1);

namespace App\Collector;

use App\Entity\BlogTranslation;
use App\Entity\Tags;
use App\Repository\BlogRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\TagsRepository;
use App\Services\PaginationService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class BlogPageCollector
{
    private \App\Repository\TagsRepository $tagsRepository;

    public function __construct(
        ProductColorRepository $colorRepository,
        ProductSizeRepository $sizeRepository,
        ProductRepository $productRepository,
        PaginationService $paginationService,
        TagsRepository $tagsRepository,
        TranslatorInterface $translator,
        ParameterBagInterface $bag,
        BlogRepository $blogRepository
    ) {
        $this->tagsRepository = $tagsRepository;
    }

    
    public function collect(BlogTranslation $blogTranslation, string $locale): array
    {
        $blog = $blogTranslation->getBlog();

        $blogTags = $this->tagsRepository->getByBlog($blog, $locale);
        $tags = $this->tagsRepository->findBy(['relatedType' => Tags::TYPE_BLOG]);

        return [
            'trans' => $blogTranslation,
            'blog_tags' => $blogTags,
            'blog' => $blog,
            'tags' => $tags,
        ];
    }
}
