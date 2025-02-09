<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Tags;
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
    private string $baseUrl;

    public function __construct(
        private readonly TagsRepository $tagsRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly TranslatorInterface $translator,
        private readonly RouterInterface $router
    ) {
        $this->baseUrl = $this->router->getContext()->getScheme().'://'.$this->router->getContext()->getHost().'/';
    }

    /**
     * @return array|\string[][][]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            SitemapPopulateEvent::class => [
                ['registerTagMenuPage'],
            ],
        ];
    }

    public function registerTagMenuPage(SitemapPopulateEvent $event): void
    {
        $tags = $this->tagsRepository->findBy(['productType' => Tags::PRODUCT_TYPE_SEASON]);

        foreach ($tags as $tag) {
            foreach ($tag->getTagTranslations() as $tagTranslation) {
                $filterTrans = $this->translator->trans('filter.seasons', [], null, $tagTranslation->getLocale());

                $url = new UrlConcrete(
                    $this->urlGenerator->generate(
                        'site.trendy_page',
                        ['_locale' => $tagTranslation->getLocale(), $filterTrans => $tagTranslation->getSlug()],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                    new DateTime(),
                    UrlConcrete::CHANGEFREQ_DAILY
                );
                
                $event->getUrlContainer()->addUrl($url,'tag_page_'.$tagTranslation->getLocale());
                $decoratedUrl = new GoogleMobileUrlDecorator($url);
                $event->getUrlContainer()->addUrl($decoratedUrl,'tag_page_mobile_'.$tagTranslation->getLocale());
            }
        }
    }
}
