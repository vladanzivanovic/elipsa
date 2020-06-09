<?php

declare(strict_types=1);

namespace App\Formatter\Site\Router;

use App\Repository\BlogTranslationRepository;

final class BlogPageRouterFormatter
{
    /**
     * @var BlogTranslationRepository
     */
    private $blogTranslationRepository;

    /**
     * @param BlogTranslationRepository     $blogTranslationRepository
     */
    public function __construct(
        BlogTranslationRepository $blogTranslationRepository
    ) {
        $this->blogTranslationRepository = $blogTranslationRepository;
    }

    /**
     * @param string $slug
     * @param string $locale
     *
     * @return string
     */
    public function localeFormatter(string $slug, string $locale): string
    {
        $fromTrans = $this->blogTranslationRepository->findOneBy(['alias' => $slug]);

        $toTrans = $fromTrans->getBlog()->getBlogTranslationByLocale($locale);

        return $toTrans->getAlias();
    }
}