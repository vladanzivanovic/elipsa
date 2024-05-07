<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Blog;
use App\Entity\BlogHasTags;
use App\Entity\BlogTranslation;
use App\Repository\BlogTranslationRepository;
use App\Repository\TagsRepository;
use App\Services\BlogImageService;
use Symfony\Component\HttpFoundation\ParameterBag;

class BlogRequestParser
{
    private BlogTranslationRepository $translationRepository;

    private BlogImageService $blogImageService;

    private TagsRepository $tagsRepository;

    private array $locales;

    public function __construct(
        BlogTranslationRepository $translationRepository,
        BlogImageService $blogImageService,
        TagsRepository $tagsRepository,
        array $locales
    ) {
        $this->translationRepository = $translationRepository;
        $this->blogImageService = $blogImageService;
        $this->locales = $locales;
        $this->tagsRepository = $tagsRepository;
    }

    /**
     * @param Blog|null    $blog
     * @throws \Doctrine\ORM\ORMException
     */
    public function parse(ParameterBag $bag, Blog $blog = null): Blog
    {
        if (!$blog instanceof Blog) {
            $blog = new Blog();
            $blog->setStatus(Blog::STATUS_PENDING);
        }

        $this->setBlogTranslation($blog, $bag);
        $this->setTags($blog, $bag->all('tags'));

        $this->blogImageService->setImages($blog->getBlogTranslationByLocale('rs'), json_decode($bag->get('images'), true));

        return $blog;
    }

    private function setBlogTranslation(Blog $blog, ParameterBag $bag): void
    {
        foreach ($this->locales as $locale) {
            $transCollection = $bag->all($locale);

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

    private function setTags(Blog $blog, array $tags): void
    {
        if (!is_null($blog->getId())) {
            $hasTags = $blog->getBlogHasTags();
            $hasTags->clear();
        }

        foreach ($tags as $tagId) {
            $tag = $this->tagsRepository->find($tagId);

            $hasTags = new BlogHasTags();
            $hasTags->setTag($tag);

            $blog->addBlogHasTag($hasTags);
        }
    }
}
