<?php

namespace App\EventListener;

use App\Entity\CategoryTranslation;
use App\Entity\Product;
use App\Entity\ProductHasImages;
use App\Entity\ProductTranslation;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use DateTime;
use Exception;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\GoogleImage;
use Presta\SitemapBundle\Sitemap\Url\GoogleImageUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\GoogleMobileUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SiteMapProductsSubscriber implements EventSubscriberInterface
{
    private \Symfony\Component\Routing\Generator\UrlGeneratorInterface $urlGenerator;

    private \Symfony\Contracts\Translation\TranslatorInterface $translator;

    private \App\Repository\ProductRepository $productRepository;

    private \App\Repository\CategoryRepository $categoryRepository;
    private \Symfony\Component\Routing\RouterInterface $router;

    private string $baseUrl;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        RouterInterface $router
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->router = $router;

        $this->baseUrl = $this->router->getContext()->getScheme().'://'.$this->router->getContext()->getHost().'/';

    }

    public static function getSubscribedEvents(): array
    {
        return [
            SitemapPopulateEvent::class => [
                ['registerProductMenuPage'],
                ['registerSingleProductPage'],
            ],
        ];
    }

    public function registerProductMenuPage(SitemapPopulateEvent $event): void
    {
        $categories = $this->categoryRepository->findAll();

        foreach ($categories as $category) {
            $transCollection = $category->getCategoryTranslations();

            /** @var CategoryTranslation $trans */
            foreach ($transCollection->getIterator() as $trans) {
                $filter = $this->translator->trans('categories', [], null, $trans->getLocale());
                $url = new UrlConcrete(
                    $this->baseUrl.$this->urlGenerator->generate(
                        'site.shop_page.'.$trans->getLocale(),
                        ['_locale' => $trans->getLocale(), 'page' => 1, 'searchData' => $filter.'/'.$trans->getSlug()],
                        UrlGeneratorInterface::RELATIVE_PATH
                    ),
                    new DateTime(),
                    UrlConcrete::CHANGEFREQ_DAILY
                );
                $event->getUrlContainer()->addUrl($url,'category_page_'.$trans->getLocale());
                $decoratedUrl = new GoogleMobileUrlDecorator($url);
                $event->getUrlContainer()->addUrl($decoratedUrl,'category_page_mobile_'.$trans->getLocale());
            }
        }
    }

    /**
     * @throws Exception
     */
    public function registerSingleProductPage(SitemapPopulateEvent $event): void
    {
        $products = $this->productRepository->findBy(['status' => Product::STATUS_ACTIVE]);

        foreach ($products as $product) {
            $transCollection = $product->getProductTranslations();

            /** @var ProductTranslation $trans */
            foreach ($transCollection->getIterator() as $trans) {
                $hasImages = $product->getProductHasImages();
                $url = new UrlConcrete(
                    $this->baseUrl.$this->urlGenerator->generate(
                        'site.product_page',
                        ['_locale' => $trans->getLocale(), 'slug' => $trans->getSlug()],
                        UrlGeneratorInterface::RELATIVE_PATH
                    ),
                    new DateTime(),
                    UrlConcrete::CHANGEFREQ_DAILY
                );

                $imageDecoratedUrl = new GoogleImageUrlDecorator($url);

                /** @var ProductHasImages $hasImage */
                foreach ($hasImages->getIterator() as $hasImage) {
                    $image = $hasImage->getImage();

                    $imageDecoratedUrl->addImage(new GoogleImage(
                        $this->baseUrl.$this->urlGenerator->generate(
                        'app.image_show',
                        ['entity' => 'product', 'name' => $image->getName(), 'filter' => 'list_thumb'],
                        UrlGeneratorInterface::RELATIVE_PATH
                    )));
                }

                $event->getUrlContainer()->addUrl($url,'products_'.$trans->getLocale());
                $decoratedUrl = new GoogleMobileUrlDecorator($url);
                $event->getUrlContainer()->addUrl($decoratedUrl,'products_mobile_'.$trans->getLocale());
                $event->getUrlContainer()->addUrl($imageDecoratedUrl,'products_images_'.$trans->getLocale());
            }
        }
    }
}
