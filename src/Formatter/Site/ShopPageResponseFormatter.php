<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

final class ShopPageResponseFormatter
{
    use FormatterTrait;

    private RouterInterface $router;

    private ParameterBagInterface $bag;

    private SessionInterface $session;

    private ProductFormatter $productFormatter;

    public function __construct(
        RouterInterface $router,
        ParameterBagInterface $bag,
        SessionInterface $session,
        ProductFormatter $productFormatter
    ) {
        $this->router = $router;
        $this->bag = $bag;
        $this->session = $session;
        $this->productFormatter = $productFormatter;
    }

    /**
     * @param array<string, array<array<string|int, mixed>>> $data
     * @param string                                         $locale
     * @param string                                         $routeName
     *
     * @return array<string, array<array<string|int, mixed>>>
     */
    public function formatResponse(
        array $data,
        string $locale,
        string $routeName
    ): array {
        $sortMapping = $this->bag->get('shop')['sort_mapping'];

        $data['products']['data'] = $this->productFormatter->getProducts($data['products']['data'], $locale);

        if (null !== $data['search_criteria'] && $data['search_criteria']->has('sort')) {
            $data['search_criteria']->set('sort', [array_search($data['search_criteria']->get('sort'), $sortMapping)]);
        }

        $data['localized_url'] = $this->router->generate($routeName, ['_locale' => $locale === 'rs' ? 'en' : 'rs', 'searchData' => $data['localized_url']]);

        return $data;
    }
}
