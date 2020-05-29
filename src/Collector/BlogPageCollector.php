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
    /**
     * @var ProductColorRepository
     */
    private $colorRepository;

    /**
     * @var ProductSizeRepository
     */
    private $sizeRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;
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
     * @param ProductColorRepository $colorRepository
     * @param ProductSizeRepository  $sizeRepository
     * @param ProductRepository      $productRepository
     * @param PaginationService      $paginationService
     * @param TagsRepository         $tagsRepository
     * @param TranslatorInterface    $translator
     * @param ParameterBagInterface  $bag
     * @param BlogRepository         $blogRepository
     */
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
        $this->colorRepository = $colorRepository;
        $this->sizeRepository = $sizeRepository;
        $this->productRepository = $productRepository;
        $this->paginationService = $paginationService;
        $this->tagsRepository = $tagsRepository;
        $this->translator = $translator;
        $this->bag = $bag;
        $this->blogRepository = $blogRepository;
    }

    /**
     * @param BlogTranslation $blogTranslation
     * @param string          $locale
     *
     * @return array
     */
    public function collect(BlogTranslation $blogTranslation, string $locale): array
    {
        $blog = $blogTranslation->getBlog();

        $blogTags = $this->tagsRepository->getByBlog($blog, $locale);
        $tags = $this->tagsRepository->findBy(['relatedType' => Tags::TYPE_BLOG, 'locale' => $locale]);

        return [
            'trans' => $blogTranslation,
            'blog_tags' => $blogTags,
            'blog' => $blog,
            'tags' => $tags,
        ];
    }
}