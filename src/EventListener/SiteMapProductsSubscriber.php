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
use Presta\SitemapBundle\Sitemap\Url\GoogleMultilangUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SiteMapProductsSubscriber implements EventSubscriberInterface
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
     * @param UrlGeneratorInterface $urlGenerator
     * @param ParameterBagInterface $parameterBag
     * @param TranslatorInterface   $translator
     * @param ProductRepository     $productRepository
     * @param CategoryRepository    $categoryRepository
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        ParameterBagInterface $parameterBag,
        TranslatorInterface $translator,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->parameterBag = $parameterBag;
        $this->translator = $translator;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            SitemapPopulateEvent::ON_SITEMAP_POPULATE => [
                ['registerStaticUrl'],
                ['registerProductMenuPage'],
                ['registerSingleProductPage'],
            ],
        ];
    }

    public function registerStaticUrl(SitemapPopulateEvent $event)
    {
       $this->_registerStaticUrl($event, 'site.home_page', UrlConcrete::CHANGEFREQ_WEEKLY, 0.7);
       $this->_registerStaticUrl($event, 'site.loyalty', UrlConcrete::CHANGEFREQ_NEVER, 0.1);
       $this->_registerStaticUrl($event, 'site.collaborator', UrlConcrete::CHANGEFREQ_NEVER, 0.1);
       $this->_registerStaticUrl($event, 'site.career_page', UrlConcrete::CHANGEFREQ_NEVER, 0.1);
       $this->_registerStaticUrl($event, 'site.policy_page', UrlConcrete::CHANGEFREQ_NEVER, 0.1);
       $this->_registerStaticUrl($event, 'site.use_conditions', UrlConcrete::CHANGEFREQ_NEVER, 0.1);
       $this->_registerStaticUrl($event, 'site.ask_us', UrlConcrete::CHANGEFREQ_NEVER, 0.1);
    }

    public function registerProductMenuPage(SitemapPopulateEvent $event)
    {
        $categories = $this->categoryRepository->findAll();
        $baseUrl = $this->parameterBag->get('url');

        foreach ($categories as $category) {
            $transCollection = $category->getCategoryTranslations();

            /** @var CategoryTranslation $trans */
            foreach ($transCollection->getIterator() as $trans) {
                $filter = $this->translator->trans('categories', [], null, $trans->getLocale());
                $url = new UrlConcrete(
                    $baseUrl.$this->urlGenerator->generate(
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
     * @param SitemapPopulateEvent $event
     *
     * @return void
     * @throws Exception
     */
    public function registerSingleProductPage(SitemapPopulateEvent $event): void
    {
        $products = $this->productRepository->findBy(['status' => Product::STATUS_ACTIVE]);
        $baseUrl = $this->parameterBag->get('url');

        foreach ($products as $product) {
            $transCollection = $product->getProductTranslations();

            /** @var ProductTranslation $trans */
            foreach ($transCollection->getIterator() as $trans) {
                $hasImages = $product->getProductHasImages();
                $url = new UrlConcrete(
                    $baseUrl.$this->urlGenerator->generate(
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
                        $baseUrl.$this->urlGenerator->generate(
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

    private function _registerStaticUrl(SitemapPopulateEvent $event, string $routeName, $changeFreq, $priority)
    {
        $locales = explode('|', $this->parameterBag->get('locales'));
        $baseUrl = $this->parameterBag->get('url');

        foreach ($locales as $locale) {
            if ($locale === 'rs') {
                continue;
            }
            $url = new UrlConcrete($baseUrl.$this->urlGenerator->generate(
                $routeName,
                [],
                UrlGeneratorInterface::RELATIVE_PATH
            ),
                new DateTime(),
                $changeFreq,
                $priority
            );
            $decoratedUrl = new GoogleMultilangUrlDecorator($url);
            $decoratedUrl->addLink($baseUrl.$this->urlGenerator->generate(
                $routeName,
                ['_locale' => $locale],
                UrlGeneratorInterface::RELATIVE_PATH
            ), $locale);

            $event->getUrlContainer()->addUrl($decoratedUrl, 'default');
        }
    }
}