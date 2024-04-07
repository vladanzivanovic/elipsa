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

    private RouterInterface $router;

    private ParameterBagInterface $bag;

    private TranslatorInterface $translator;

    private ShopPageRouterFormatter $shopPageRouterFormatter;

    private TagUrlLocalizationFormatter $tagUrlLocalizationFormatter;

    private ProductPageRouterFormatter $productPageRouterFormatter;

    private BlogPageRouterFormatter $blogPageRouterFormatter;

    private JobPageRouterFormatter $jobPageRouterFormatter;
    private array $siteInfoText;

    public function __construct(
        RouterInterface $router,
        ParameterBagInterface $bag,
        TranslatorInterface $translator,
        ShopPageRouterFormatter $shopPageRouterFormatter,
        TagUrlLocalizationFormatter $tagUrlLocalizationFormatter,
        ProductPageRouterFormatter $productPageRouterFormatter,
        BlogPageRouterFormatter $blogPageRouterFormatter,
        JobPageRouterFormatter $jobPageRouterFormatter,
        array $siteInfoText
    ) {
        $this->router = $router;
        $this->bag = $bag;
        $this->translator = $translator;
        $this->shopPageRouterFormatter = $shopPageRouterFormatter;
        $this->tagUrlLocalizationFormatter = $tagUrlLocalizationFormatter;
        $this->productPageRouterFormatter = $productPageRouterFormatter;
        $this->blogPageRouterFormatter = $blogPageRouterFormatter;
        $this->jobPageRouterFormatter = $jobPageRouterFormatter;
        $this->siteInfoText = $siteInfoText;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('url_locale', [$this, 'generateUrlLocale']),
        ];
    }

    public function generateUrlLocale(string $routeName, array $routeParams, string $fromLocale, string $toLocale): string
    {
        if (($routeName === 'site.shop_page' || $routeName === 'site.trendy_page') && isset($routeParams['searchData'])) {
            $routeParams['searchData'] = $this->shopPageRouterFormatter->localeFormatter($routeParams['searchData'], $toLocale);
        }

        if ($routeName === 'site.blog_list_page' && isset($routeParams['tag'])) {
            $tag = $this->tagUrlLocalizationFormatter->localeFormatter($routeParams['tag'], $toLocale);

            $routeParams['tag'] = $tag ?? '#';
        }

        if ($routeName === 'site.product_page') {
            $slug = $this->productPageRouterFormatter->localeFormatter($routeParams['slug'], $toLocale);
            $routeParams['slug'] = $slug ?? '#';
        }

        if ($routeName === 'site.blog_detailed_page') {
            $tag = $this->blogPageRouterFormatter->localeFormatter($routeParams['slug'], $toLocale);

            $routeParams['slug'] = $tag ?? '#';
        }

        if ($routeName === 'site.jobs_detail_page') {
            $routeParams['slug'] = $this->jobPageRouterFormatter->localeFormatter($routeParams['slug'], $toLocale);
        }

        if ($routeName === 'site.company_text') {
            $routeParams['type'] = $this->getTextLocaleSlug($routeParams['type'], $fromLocale, $toLocale);
        }

        if ($toLocale !== 'rs') {
            $routeParams['_locale'] = $toLocale;
            $routeName = $this->getRouteName($routeName, $toLocale);

        }

        if ($toLocale === 'rs') {
            unset($routeParams['_locale']);
            $routeName = $this->getRouteName(str_replace('site_locale_', '', $routeName), 'rs');
        }

        return $this->router->generate($routeName, $routeParams);
    }

    private function getRouteName(string $routeName, string $locale)
    {
        $routeCollection = $this->router->getRouteCollection();
        $route = $routeCollection->get($routeName);

        if (!$route instanceof Route) {
            $routeCollection->get($routeName.'.'.$locale);
            $routeName = $routeName.'.'.$locale;
        }

        return $routeName;
    }

    private function getTextLocaleSlug(string $slug, string $fromLocale, string $toLocale): ?string
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
