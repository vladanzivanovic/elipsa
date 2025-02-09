<?php

namespace App\EventListener;

use App\Repository\DescriptionRepository;
use DateTime;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\GoogleMultilangUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class SiteMapStaticSubscriber implements EventSubscriberInterface
{
    private string $baseUrl;

    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly RouterInterface $router,
        private readonly ParameterBagInterface $parameterBag,
        private readonly DescriptionRepository $descriptionRepository,
    ) {
        $this->baseUrl = $this->router->getContext()->getScheme().'://'.$this->router->getContext()->getHost().'/';
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SitemapPopulateEvent::class => [
                ['registerStaticUrl'],
            ],
        ];
    }

    public function registerStaticUrl(SitemapPopulateEvent $event): void
    {
        $this->_registerStaticUrl($event, 'site.home_page', UrlConcrete::CHANGEFREQ_WEEKLY, 0.7);

        $this->registerStaticTexts($event);
    }

    private function registerStaticTexts(SitemapPopulateEvent $event)
    {
        $textList = $this->descriptionRepository->findAll();
        $siteInfoText = $this->parameterBag->get('site_info_texts');

        foreach ($textList as $text) {
            $url = $this->urlGenerator->generate(
                'site.company_text',
                ['_locale' => $text->getLocale(), 'type' => $siteInfoText[$text->getType()]['slug'][$text->getLocale()]],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $urlConcrete = new UrlConcrete(
                $url,
                new DateTime(),
                UrlConcrete::CHANGEFREQ_NEVER,
                0.1
            );

            $decoratedUrl = new GoogleMultilangUrlDecorator($urlConcrete);
            $decoratedUrl->addLink(
                $url,
                $text->getLocale()
            );

            $event->getUrlContainer()->addUrl($decoratedUrl, 'default');
        }
    }

    private function _registerStaticUrl(SitemapPopulateEvent $event, string $routeName, string $changeFreq, float $priority): void
    {
        $locales = $this->parameterBag->get('locales');

        foreach ($locales as $locale) {
            $url = new UrlConcrete( $this->urlGenerator->generate(
                $routeName,
                ['_locale' => $locale],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
                new DateTime(),
                $changeFreq,
                $priority
            );
            $decoratedUrl = new GoogleMultilangUrlDecorator($url);
            $decoratedUrl->addLink($this->urlGenerator->generate(
                $routeName,
                ['_locale' => $locale],
                UrlGeneratorInterface::ABSOLUTE_URL
            ), $locale);

            $event->getUrlContainer()->addUrl($decoratedUrl, 'default');
        }
    }
}
