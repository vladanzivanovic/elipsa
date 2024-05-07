<?php

declare(strict_types=1);

namespace App\Formatter\Site\Router;

use App\Repository\BlogTranslationRepository;
use App\Repository\TagTranslationRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class BlogPageRouterFormatter
{
    public function __construct(
        private readonly BlogTranslationRepository $blogTranslationRepository,
        private readonly RouterInterface $router,
        private readonly TagTranslationRepository $tagTranslationRepository,
        private readonly array $locales,
    ) {}

    /**
     *
     * @return string
     */
    public function localeFormatter(string $slug, string $locale): ?string
    {
        $fromTrans = $this->blogTranslationRepository->findOneBy(['alias' => $slug]);

        $toTrans = $fromTrans->getBlog()->getBlogTranslationByLocale($locale);

        return null !== $toTrans ? $toTrans->getAlias() : null;
    }

    public function createLocalizedLinks(
        null|string $tagSlug,
    ): array {
        $translations = [];

        foreach ($this->locales as $locale) {
            $searchCriteria = [];

            $tagTrans = $this->getTag($tagSlug, $locale);

            $urlParams = [
                '_locale' => $locale,
                'page' => 1,
                'tag' => $tagTrans,
            ];

            $translations[$locale]['site.blog_list_page'] = $this->router->generate(
                'site.blog_list_page',
                $urlParams,
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }

        return $translations;
    }

    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getTag(null|string $tagSlug, string $locale): null|string
    {
        if (null === $tagSlug) {
            return null;
        }

        return $this->tagTranslationRepository->getForLocalization($tagSlug, $locale);
    }
}
