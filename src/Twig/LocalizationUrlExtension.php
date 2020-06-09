<?php

declare(strict_types=1);

namespace App\Twig;

use App\Formatter\Site\Router\BlogListRouterFormatter;
use App\Formatter\Site\Router\BlogPageRouterFormatter;
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

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ShopPageRouterFormatter
     */
    private $shopPageRouterFormatter;
    /**
     * @var BlogListRouterFormatter
     */
    private $blogListRouterFormatter;
    /**
     * @var ProductPageRouterFormatter
     */
    private $productPageRouterFormatter;
    /**
     * @var BlogPageRouterFormatter
     */
    private $blogPageRouterFormatter;

    /**
     * @param RouterInterface            $router
     * @param ParameterBagInterface      $bag
     * @param TranslatorInterface        $translator
     * @param ShopPageRouterFormatter    $shopPageRouterFormatter
     * @param BlogListRouterFormatter    $blogListRouterFormatter
     * @param ProductPageRouterFormatter $productPageRouterFormatter
     * @param BlogPageRouterFormatter    $blogPageRouterFormatter
     */
    public function __construct(
        RouterInterface $router,
        ParameterBagInterface $bag,
        TranslatorInterface $translator,
        ShopPageRouterFormatter $shopPageRouterFormatter,
        BlogListRouterFormatter $blogListRouterFormatter,
        ProductPageRouterFormatter $productPageRouterFormatter,
        BlogPageRouterFormatter $blogPageRouterFormatter
    ) {
        $this->router = $router;
        $this->bag = $bag;
        $this->translator = $translator;
        $this->shopPageRouterFormatter = $shopPageRouterFormatter;
        $this->blogListRouterFormatter = $blogListRouterFormatter;
        $this->productPageRouterFormatter = $productPageRouterFormatter;
        $this->blogPageRouterFormatter = $blogPageRouterFormatter;
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

    public function generateUrlLocale(string $routeName, array $routeParams, string $fromLocale, string $toLocale)
    {
        if (($routeName === 'site.shop_page' || $routeName === 'site.trendy_page') && isset($routeParams['searchData'])) {
            $routeParams['searchData'] = $this->shopPageRouterFormatter->localeFormatter($routeParams['searchData'], $toLocale);
        }

        if ($routeName === 'site.blog_list_page' && isset($routeParams['tag'])) {
            $routeParams['tag'] = $this->blogListRouterFormatter->localeFormatter($routeParams['tag'], $toLocale);
        }

        if ($routeName === 'site.product_page') {
            $routeParams['slug'] = $this->productPageRouterFormatter->localeFormatter($routeParams['slug'], $toLocale);
        }

        if ($routeName === 'site.blog_detailed_page') {
            $routeParams['slug'] = $this->blogPageRouterFormatter->localeFormatter($routeParams['slug'], $toLocale);
        }

        if ($toLocale != 'rs') {
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


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'localized_url_extension';
    }
}
