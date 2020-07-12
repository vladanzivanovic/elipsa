<?php

namespace App\EventListener;

use App\Entity\Blog;
use App\Entity\BlogTranslation;
use App\Repository\BlogRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use DateTime;
use Exception;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\GoogleImage;
use Presta\SitemapBundle\Sitemap\Url\GoogleImageUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SiteMapBlogSubscriber implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var BlogRepository
     */
    private $blogRepository;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param ParameterBagInterface $parameterBag
     * @param TranslatorInterface   $translator
     * @param ProductRepository     $productRepository
     * @param CategoryRepository    $categoryRepository
     * @param BlogRepository        $blogRepository
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        ParameterBagInterface $parameterBag,
        TranslatorInterface $translator,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        BlogRepository $blogRepository
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->parameterBag = $parameterBag;
        $this->translator = $translator;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->blogRepository = $blogRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            SitemapPopulateEvent::ON_SITEMAP_POPULATE => [
                ['registerSingleBlogUrl'],
            ],
        ];
    }

    public function registerSingleBlogUrl(SitemapPopulateEvent $event)
    {
        $blogs = $this->blogRepository->findBy(['status' => Blog::STATUS_ACTIVE]);
        $baseUrl = $this->parameterBag->get('url');

        foreach ($blogs as $blog) {
            $transCollection = $blog->getBlogTranslations();

            /** @var BlogTranslation $trans */
            foreach ($transCollection->getIterator() as $trans) {
                $url = new UrlConcrete(
                    $baseUrl.$this->urlGenerator->generate(
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
                    $baseUrl.$this->urlGenerator->generate(
                    'app.image_show',
                    ['entity' => 'blog', 'name' => $image->getName(), 'filter' => 'blog_list'],
                    UrlGeneratorInterface::RELATIVE_PATH
                )));
                $event->getUrlContainer()->addUrl($imageDecoratedUrl,'blog_images_'.$trans->getLocale());
            }
        }
    }
}