<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Blog;
use App\Entity\BlogHasTags;
use App\Entity\BlogTranslation;
use App\Repository\BlogHasTagsRepository;
use App\Repository\BlogTranslationRepository;
use App\Services\BlogImageService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class BlogRequestParser
{
    /**
     * @var BlogTranslationRepository
     */
    private $translationRepository;
    /**
     * @var BlogImageService
     */
    private $blogImageService;
    /**
     * @var BlogHasTagsRepository
     */
    private $hasTagsRepository;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * BlogRequestParser constructor.
     *
     * @param BlogTranslationRepository $translationRepository
     * @param BlogImageService          $blogImageService
     * @param BlogHasTagsRepository     $hasTagsRepository
     * @param ParameterBagInterface     $parameterBag
     */
    public function __construct(
        BlogTranslationRepository $translationRepository,
        BlogImageService $blogImageService,
        BlogHasTagsRepository $hasTagsRepository,
        ParameterBagInterface $parameterBag
    ) {
        $this->translationRepository = $translationRepository;
        $this->blogImageService = $blogImageService;
        $this->hasTagsRepository = $hasTagsRepository;
        $this->parameterBag = $parameterBag;
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

        $languages = $this->setLanguageArray($bag);

        foreach ($languages as $locale => $langBag) {
            $blogTranslation = $this->translationRepository->findOneBy(['blog' => $blog, 'locale' => $locale]);

            if (!$blogTranslation instanceof BlogTranslation) {
                $blogTranslation = new BlogTranslation();
            }

            $blogTranslation->setTitle($bag->get($locale.'_title'))
                ->setShortDescription($bag->get($locale.'_short_description'))
                ->setDescription($bag->get($locale.'_description'))
                ->setLocale($locale)
                ->setBlog($blog);

            $blog->addBlogTranslation($blogTranslation);
        }
    }

    private function setLanguageArray(ParameterBag $bag): array
    {
        $langArray = [];

        $locales = explode('|', $this->parameterBag->get('locales'));

        foreach ($bag->all() as $key => $item) {
            $langCode = substr($key, 0, 2);

            if (false === in_array($langCode, $locales)) {
                continue;
            }

            if (false === array_key_exists($langCode, $langArray)) {
                $langArray[$langCode] = new ParameterBag();
            }

            /** @var ParameterBag $langBag */
            $langBag = $langArray[$langCode];
            $langBag->set($key, $item);
        }

        return $langArray;
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

        foreach ($tags as $tag) {
            $hasTags = new BlogHasTags();
            $hasTags->setTag($tag);

            $blog->addBlogHasTag($hasTags);
        }
    }
}