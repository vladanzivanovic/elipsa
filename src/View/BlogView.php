<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Blog;
use App\Entity\Image;
use App\Entity\Tags;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class BlogView
{
    public function __construct(
        private readonly TagView $tagView,
        private readonly ImageView $imageView,
        private readonly string $defaultLocale,
        private readonly array $locales,
        private readonly RouterInterface $router,
    ){}

    public function editView(Blog $blog): array
    {
        $view = $this->defaultViewData($blog);

        $view['media']['images'] = [$this->imageView->view($blog->getImage(), 'blog')];
        $view['available_countries'] = $blog->getAvailableCountries();

        foreach ($blog->getBlogHasTags() as $blogHasTag) {
            $view['tags'][] = $blogHasTag->getTag()->getId();
        }

        return $view;
    }

    public function view(Blog $blog): array
    {
        $view = $this->defaultViewData($blog);

        foreach ($blog->getBlogHasTags() as $blogHasTag) {
            $view['tags'][] = $this->tagView->view($blogHasTag->getTag());
        }

        $view['media']['images'] = [$this->imageView->view($blog->getImage(), 'blog', 'blog_list')];
        $view['_links'] = $this->getLinks($view['translations']);

        return $view;
    }

    private function defaultViewData(Blog $blog): array
    {
        $createdAt = $blog->getCreatedAt();

        return [
            'id' => $blog->getId(),
            'tags' => null,
            'translations' => $this->getTranslationValues($blog),
            'date' => [
                'day' => $createdAt->format('d'),
                'month' => $createdAt->format('m'),
                'formatted' => $createdAt->format('d.m.Y'),
            ],
            'media' => [
                'images' => null,
            ],
        ];
    }

    private function getTranslationValues(Blog $blog): array
    {
        $translations = [];

        foreach ($this->locales as $locale) {
            $trans = $blog->getBlogTranslationByLocale($locale);

            if (null === $trans) {
                continue;
            }

            $translations[$locale] = [
                'title' => $trans->getTitle(),
                'slug' => $trans->getAlias(),
                'short_description' => $trans->getShortDescription(),
                'description' => $trans->getDescription(),
            ];
        }

        return $translations;
    }

    private function getLinks(array $translations): array
    {
        $links = [];

        foreach ($translations as $locale => $translation) {
            $params = ['slug' => $translation['slug'], '_locale' => $locale];

            $links[$locale] = $this->router->generate(
                'site.blog_detailed_page',
                $params,
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }

        return $links;
    }
}
