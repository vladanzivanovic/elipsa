<?php

namespace App\EventListener;

use App\Entity\Blog;
use App\Entity\BlogTranslation;
use App\Repository\BlogRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use DateTime;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\GoogleImage;
use Presta\SitemapBundle\Sitemap\Url\GoogleImageUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SiteMapBlogSubscriber implements EventSubscriberInterface
{
    private \Symfony\Component\Routing\Generator\UrlGeneratorInterface $urlGenerator;

    private \App\Repository\BlogRepository $blogRepository;

    private \Symfony\Component\Routing\RouterInterface $router;

    private string $baseUrl;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        BlogRepository $blogRepository,
        RouterInterface $router
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->blogRepository = $blogRepository;
        $this->router = $router;

        $this->baseUrl = $this->router->getContext()->getScheme().'://'.$this->router->getContext()->getHost().'/';
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SitemapPopulateEvent::class => [
                ['registerSingleBlogUrl'],
            ],
        ];
    }

    public function registerSingleBlogUrl(SitemapPopulateEvent $event): void
    {
        $blogs = $this->blogRepository->findBy(['status' => Blog::STATUS_ACTIVE]);

        foreach ($blogs as $blog) {
            $transCollection = $blog->getBlogTranslations();

            /** @var BlogTranslation $trans */
            foreach ($transCollection->getIterator() as $trans) {
                $url = new UrlConcrete(
                    $this->baseUrl.$this->urlGenerator->generate(
                        'site.blog_detailed_page',
                        ['_locale' => $trans->getLocale(), 'slug' => $trans->getAlias()],
                        UrlGeneratorInterface::RELATIVE_PATH
                    ),
                    new DateTime(),
                    UrlConcrete::CHANGEFREQ_DAILY
                );
                $event->getUrlContainer()->addUrl($url,'blog_page_'.$trans->getLocale());

                $image = $blog->getImage();

                $imageDecoratedUrl = new GoogleImageUrlDecorator($url);
                $imageDecoratedUrl->addImage(new GoogleImage(
                    $this->baseUrl.$this->urlGenerator->generate(
                    'app.image_show',
                    ['entity' => 'blog', 'name' => $image->getName(), 'filter' => 'blog_list'],
                    UrlGeneratorInterface::RELATIVE_PATH
                )));
                $event->getUrlContainer()->addUrl($imageDecoratedUrl,'blog_images_'.$trans->getLocale());
            }
        }
    }
}
