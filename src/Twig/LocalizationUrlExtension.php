<?php

declare(strict_types=1);

namespace App\Twig;

use App\Formatter\Site\Router\TagUrlLocalizationFormatter;
use App\Formatter\Site\Router\BlogPageRouterFormatter;
use App\Formatter\Site\Router\JobPageRouterFormatter;
use App\Formatter\Site\Router\ProductPageRouterFormatter;
use App\Formatter\Site\Router\ShopPageRouterFormatter;
use App\ShopTrait;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LocalizationUrlExtension extends AbstractExtension
{
    use ShopTrait;

    public function __construct(
        private readonly RouterInterface $router,
        private readonly ParameterBagInterface $bag,
        private readonly TranslatorInterface $translator,
        private readonly ShopPageRouterFormatter $shopPageRouterFormatter,
        private readonly TagUrlLocalizationFormatter $tagUrlLocalizationFormatter,
        private readonly ProductPageRouterFormatter $productPageRouterFormatter,
        private readonly BlogPageRouterFormatter $blogPageRouterFormatter,
        private readonly JobPageRouterFormatter $jobPageRouterFormatter,
        private readonly array $siteInfoText,
        private readonly string $defaultLocale,
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('url_locale', [$this, 'generateUrlLocale']),
        ];
    }

    public function generateUrlLocale(string $routeName, array $routeParams, string $fromLocale, string $toLocale): string
    {
        match ($routeName) {
            'site.product_page' => $this->productPage($routeParams, $toLocale),
            'site.blog_detailed_page' => $this->blogDetailPage($routeParams, $toLocale),
            'site.jobs_detail_page' => $routeParams['slug'] = $this->jobPageRouterFormatter->localeFormatter($routeParams['slug'], $toLocale),
            'site.company_text' => $routeParams['type'] = $this->getTextLocaleSlug($routeParams['type'], $fromLocale, $toLocale),
            default => $this->default($routeName, $routeParams, $toLocale),
        };

        return $this->router->generate($routeName, $routeParams);
    }

    private function productPage(array &$routeParams, string $toLocale): void
    {
        $slug = $this->productPageRouterFormatter->localeFormatter($routeParams['slug'], $toLocale);
        $routeParams['slug'] = $slug ?? '#';
    }

    private function default(string &$routeName, array &$routeParams, string $toLocale): void
    {
        $routeParams['_locale'] = $toLocale;
        $routeName = $this->getRouteName(str_replace('site_locale_', '', $routeName), $toLocale);
    }

    private function blogDetailPage(array &$routeParams, string $toLocale): void
    {
        $tag = $this->blogPageRouterFormatter->localeFormatter($routeParams['slug'], $toLocale);
        $routeParams['slug'] = $tag ?? '#';
    }

    private function getRouteName(string $routeName, string $locale): string
    {
        $routeCollection = $this->router->getRouteCollection();
        $route = $routeCollection->get($routeName);

        if (!$route instanceof Route) {
            $routeCollection->get($routeName.'.'.$locale);
            $routeName = $routeName.'.'.$locale;
        }

        return $routeName;
    }

    private function getTextLocaleSlug(string $slug, string $fromLocale, string $toLocale): null|string
    {
        foreach ($this->siteInfoText as $infoText) {
            if ($infoText['slug'][$fromLocale] === $slug) {
                return $infoText['slug'][$toLocale];
            }
        }

        return null;
    }

    public function getName(): string
    {
        return 'localized_url_extension';
    }
}
