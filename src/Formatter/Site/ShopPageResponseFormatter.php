<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Entity\User;
use App\View\TagView;
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

    private TagView $tagView;

    public function __construct(
        RouterInterface $router,
        ParameterBagInterface $bag,
        SessionInterface $session,
        ProductFormatter $productFormatter,
        TagView $tagView
    ) {
        $this->router = $router;
        $this->bag = $bag;
        $this->session = $session;
        $this->productFormatter = $productFormatter;
        $this->tagView = $tagView;
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
        string $routeName,
        ?User $user = null
    ): array {
        $sortMapping = $this->bag->get('shop')['sort_mapping'];

        $data['products']['data'] = $this->productFormatter->getProducts($data['products']['data'], $locale, $user);

        if (null !== $data['search_criteria'] && $data['search_criteria']->has('sort')) {
            $data['search_criteria']->set('sort', [array_search($data['search_criteria']->get('sort'), $sortMapping)]);
        }

        $data['localized_url'] = $this->router->generate($routeName, ['_locale' => $locale === 'rs' ? 'en' : 'rs', 'searchData' => $data['localized_url']]);

        if (isset($data['tags'])) {
            $tagList = [];

            foreach ($data['tags'] as $tag) {
                $tagList[] = $this->tagView->view($tag);
            }

            $data['tags'] = $tagList;
        }

        return $data;
    }
}
