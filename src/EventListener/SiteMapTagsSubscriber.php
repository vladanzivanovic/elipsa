<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Repository\TagsRepository;
use DateTime;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\GoogleMobileUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SiteMapTagsSubscriber implements EventSubscriberInterface
{
    /**
     * @var TagsRepository
     */
    private $tagsRepository;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @param TagsRepository        $tagsRepository
     * @param UrlGeneratorInterface $urlGenerator
     * @param TranslatorInterface   $translator
     * @param RouterInterface       $router
     */
    public function __construct(
        TagsRepository $tagsRepository,
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator,
        RouterInterface $router
    ) {
        $this->tagsRepository = $tagsRepository;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->router = $router;

        $this->baseUrl = $this->router->getContext()->getScheme().'://'.$this->router->getContext()->getHost().'/';
    }

    /**
     * @return array|\string[][][]
     */
    public static function getSubscribedEvents()
    {
        return [
            SitemapPopulateEvent::ON_SITEMAP_POPULATE => [
                ['registerTagMenuPage'],
            ],
        ];
    }

    public function registerTagMenuPage(SitemapPopulateEvent $event)
    {
        $tags = $this->tagsRepository->findAll();

        foreach ($tags as $tag) {
            $locale = $tag->getLocale();
            $filter = $this->translator->trans('tags', [], null, $locale);

            $url = new UrlConcrete(
                $this->baseUrl.$this->urlGenerator->generate(
                    'site.trendy_page.'.$locale,
                    ['_locale' => $locale, 'page' => 1, 'searchData' => $filter.'/'.$tag->getSlug()],
                    UrlGeneratorInterface::RELATIVE_PATH
                ),
                new DateTime(),
                UrlConcrete::CHANGEFREQ_DAILY
            );
            $event->getUrlContainer()->addUrl($url,'tag_page_'.$locale);
            $decoratedUrl = new GoogleMobileUrlDecorator($url);
            $event->getUrlContainer()->addUrl($decoratedUrl,'tag_page_mobile_'.$locale);
        }
    }
}