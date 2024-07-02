<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Blog;
use App\Entity\BlogHasTags;
use App\Entity\BlogTranslation;
use App\Repository\BlogTranslationRepository;
use App\Repository\TagsRepository;
use App\Request\Dto\Admin\BlogEditRequestDto;
use App\Services\BlogImageService;

class BlogRequestParser
{
    public function __construct(
        private readonly BlogTranslationRepository $translationRepository,
        private readonly BlogImageService $blogImageService,
        private readonly TagsRepository $tagsRepository,
        private readonly array $locales,
        private readonly string $defaultLocale,
    ) {}

    /**
     * @param Blog|null    $blog
     * @throws \Doctrine\ORM\ORMException
     */
    public function parse(BlogEditRequestDto $blogEditRequestDto, Blog $blog = null): Blog
    {
        if (!$blog instanceof Blog) {
            $blog = new Blog();
            $blog->setStatus(Blog::STATUS_PENDING);
        }

        $this->setBlogTranslation($blog, $blogEditRequestDto);
        $this->setTags($blog, $blogEditRequestDto);
        $blog->setAvailableCountries($blogEditRequestDto->availableCountries);

        $countryLocale = $this->defaultLocale;

        if (false === in_array($countryLocale, $blogEditRequestDto->availableCountries, true)) {
            $countryLocale = $blogEditRequestDto->availableCountries[0];
        }

        $this->blogImageService->setImages($blog->getBlogTranslationByLocale($countryLocale), $blogEditRequestDto->images);

        return $blog;
    }

    private function setBlogTranslation(Blog $blog, BlogEditRequestDto $blogEditRequestDto): void
    {
        foreach ($this->locales as $locale) {
            $transCollection = $blogEditRequestDto->translations[$locale] ?? null;

            if (null === $transCollection) {
                continue;
            }

            $blogTranslation = $this->translationRepository->findOneBy(['blog' => $blog, 'locale' => $locale]);

            if (!$blogTranslation instanceof BlogTranslation) {
                $blogTranslation = new BlogTranslation();
            }

            $blogTranslation->setTitle($transCollection['title'])
                ->setShortDescription($transCollection['short_description'])
                ->setDescription($transCollection['description'])
                ->setLocale($locale)
                ->setBlog($blog);

            $blog->addBlogTranslation($blogTranslation);
        }
    }

    private function setTags(Blog $blog, BlogEditRequestDto $blogEditRequestDto): void
    {
        $blog->getBlogHasTags()->clear();

        foreach ($blogEditRequestDto->tags as $tagId) {
            $tag = $this->tagsRepository->find($tagId);

            $hasTags = new BlogHasTags();
            $hasTags->setTag($tag);

            $blog->addBlogHasTag($hasTags);
        }
    }
}
