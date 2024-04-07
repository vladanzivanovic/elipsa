<?php

namespace App\EventListener;

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
    private \Symfony\Component\Routing\Generator\UrlGeneratorInterface $urlGenerator;

    private \Symfony\Component\Routing\RouterInterface $router;

    private string $baseUrl;
    private \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $parameterBag;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        RouterInterface $router,
        ParameterBagInterface $parameterBag
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->router = $router;
        $this->parameterBag = $parameterBag;

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
       $this->_registerStaticUrl($event, 'site.loyalty', UrlConcrete::CHANGEFREQ_NEVER, 0.1);
       $this->_registerStaticUrl($event, 'site.collaborator', UrlConcrete::CHANGEFREQ_NEVER, 0.1);
       $this->_registerStaticUrl($event, 'site.career_page', UrlConcrete::CHANGEFREQ_NEVER, 0.1);
       $this->_registerStaticUrl($event, 'site.policy_page', UrlConcrete::CHANGEFREQ_NEVER, 0.1);
       $this->_registerStaticUrl($event, 'site.use_conditions', UrlConcrete::CHANGEFREQ_NEVER, 0.1);
       $this->_registerStaticUrl($event, 'site.ask_us', UrlConcrete::CHANGEFREQ_NEVER, 0.1);
    }

    private function _registerStaticUrl(SitemapPopulateEvent $event, string $routeName, $changeFreq, $priority): void
    {
        $locales = explode('|', $this->parameterBag->get('locales'));

        foreach ($locales as $locale) {
            if ($locale === 'rs') {
                continue;
            }
            $url = new UrlConcrete($this->baseUrl.$this->urlGenerator->generate(
                $routeName,
                [],
                UrlGeneratorInterface::RELATIVE_PATH
            ),
                new DateTime(),
                $changeFreq,
                $priority
            );
            $decoratedUrl = new GoogleMultilangUrlDecorator($url);
            $decoratedUrl->addLink($this->baseUrl.$this->urlGenerator->generate(
                $routeName,
                ['_locale' => $locale],
                UrlGeneratorInterface::RELATIVE_PATH
            ), $locale);

            $event->getUrlContainer()->addUrl($decoratedUrl, 'default');
        }
    }
}
