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
        string $locales
    ) {
        $this->translationRepository = $translationRepository;
        $this->blogImageService = $blogImageService;
        $this->locales = explode('|', $locales);
        $this->tagsRepository = $tagsRepository;
    }

    /**
     * @param ParameterBag $bag
     * @param Blog|null    $blog
     *
     * @return Blog
     * @throws \Doctrine\ORM\ORMException
     */
    public function parse(ParameterBag $bag, Blog $blog = null): Blog
    {
        if (!$blog instanceof Blog) {
            $blog = new Blog();
            $blog->setStatus(Blog::STATUS_PENDING);
        }

        $this->setBlogTranslation($blog, $bag);
        $this->setTags($blog, $bag->get('tags'));

        $this->blogImageService->setImages($blog->getBlogTranslationByLocale('rs'), json_decode($bag->get('images'), true));

        return $blog;
    }

    private function setBlogTranslation(Blog $blog, ParameterBag $bag)
    {
        foreach ($this->locales as $locale) {
            $transCollection = $bag->get($locale);

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

    /**
     * @param Blog  $blog
     * @param array $tags
     */
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
