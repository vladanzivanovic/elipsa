<?php

declare(strict_types=1);

namespace App\Formatter\Site\Router;

use App\Repository\CategoryTranslationRepository;
use App\Repository\ColorTranslationRepository;
use App\Repository\TagTranslationRepository;
use App\Request\Dto\ShopListRequestDto;
use App\Request\Dto\ShopPageOptionsDto;
use App\ShopTrait;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ShopPageRouterFormatter
{
    use ShopTrait;

    public function __construct(
        private readonly ParameterBagInterface $bag,
        private readonly TranslatorInterface $translator,
        private readonly ColorTranslationRepository $colorTranslationRepository,
        private readonly CategoryTranslationRepository $categoryTranslationRepository,
        private readonly TagUrlLocalizationFormatter $tagUrlLocalizationFormatter,
        private readonly TagTranslationRepository $tagTranslationRepository,
        private readonly RouterInterface $router,
        private readonly string $defaultLocale,
        private readonly array $locales,
    ) {}

    public function createLocalizedLinks(
        ShopPageOptionsDto $shopPageOptionsDto,
        ShopListRequestDto $shopListRequestDto,
        array $routerNames,
    ): array {
        $translations = [];

        foreach ($this->locales as $locale) {
            $searchCriteria = [];

            $this->getSizes($searchCriteria, $shopListRequestDto->size, $locale);
            $this->getColor($searchCriteria, $shopListRequestDto->color, $locale);
            $this->getCategories($searchCriteria, $shopListRequestDto->categories, $locale);
            $this->getTags($searchCriteria, $shopListRequestDto->collection, 'filter.collections', $locale);
            $this->getTags($searchCriteria, $shopListRequestDto->season, 'filter.seasons', $locale);
            $this->getTags($searchCriteria, $shopListRequestDto->season, 'filter.seasons', $locale);
            $this->getTags($searchCriteria, $shopListRequestDto->attribute, 'filter.attributes', $locale);
            $this->getTags($searchCriteria, $shopListRequestDto->promotions, 'filter.promotions', $locale);
            $this->getPrices($searchCriteria, $shopListRequestDto->price, $locale);
            $this->getSearch($searchCriteria, $shopListRequestDto->search, $locale);
            $this->getSort($searchCriteria, $shopPageOptionsDto->sort, $locale);
            $this->getLimit($searchCriteria, $shopPageOptionsDto->limit, $locale);

            $urlParams = [
                '_locale' => $locale,
            ];

            foreach ($routerNames as $routerName) {
                $translations[$locale][$routerName] = $this->router->generate(
                    $routerName,
                    $urlParams + $searchCriteria,
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
            }
        }

        return $translations;
    }

    private function getLimit(array &$searchCriteria, int $limit, string $locale): void
    {
        if (ShopPageOptionsDto::DEFAULT_MAX_PRODUCT_NUMBERS === $limit) {
            return;
        }

        $transName = $this->translator->trans('filter.limit', [], 'messages', $locale);

        $searchCriteria[$transName] = $limit;
    }

    private function getSort(array &$searchCriteria, ?string $sort, string $locale): void
    {
        if (null === $sort) {
            return;
        }

        $transName = $this->translator->trans('filter.sort', [], 'messages', $locale);

        $searchCriteria[$transName] = $sort;
    }

    private function getSearch(array &$searchCriteria, ?string $search, string $locale): void
    {
        if (null === $search) {
            return;
        }

        $transName = $this->translator->trans('filter.search', [], 'messages', $locale);

        $searchCriteria[$transName] = $search;
    }

    private function getSizes(array &$searchCriteria, ?array $sizes, string $locale): void
    {
        if (null === $sizes) {
            return;
        }

        $transName = $this->translator->trans('filter.sizes', [], 'messages', $locale);

        $searchCriteria[$transName] = implode('+', $sizes);
    }

    private function getPrices(array &$searchCriteria, ?array $prices, $locale): void
    {
        if (null === $prices) {
            return;
        }

        $transName = $this->translator->trans('filter.price', [], 'messages', $locale);

        $searchCriteria[$transName] = implode('+', $prices);
    }

    private function getColor(array &$searchCriteria, ?array $colors, string $locale): void
    {
        if (null === $colors) {
            return;
        }

        $transName = $this->translator->trans('filter.colors', [], 'messages', $locale);

        $trans = [];

        foreach ($colors as $color) {
            try {
            $trans[] = $this->colorTranslationRepository->getForLocalization($color, $locale);
            } catch (\Throwable $exception) {
                $trans[] = $this->colorTranslationRepository->getForLocalization($color, $this->defaultLocale);
            }
        }

        $searchCriteria[$transName] = implode('+', $trans);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    private function getCategories(array &$searchCriteria, ?array $categories, string $locale): void
    {
        if (null === $categories) {
            return;
        }

        $transName = $this->translator->trans('filter.categories', [], 'messages', $locale);

        $trans = [];

        foreach ($categories as $category) {
            try {
                $trans[] = $this->categoryTranslationRepository->getForLocalization($category, $locale);
            } catch (\Throwable $exception) {
                $trans[] = $this->categoryTranslationRepository->getForLocalization($category, $this->defaultLocale);
            }
        }

        $searchCriteria[$transName] = implode('+', $trans);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    private function getTags(array &$searchCriteria, ?array $tags, string $productType, string $locale): void
    {
        if (null === $tags) {
            return;
        }

        $transName = $this->translator->trans($productType, [], 'messages', $locale);

        $trans = [];

        foreach ($tags as $tag) {
            try {
            $trans[] = $this->tagTranslationRepository->getForLocalization($tag, $locale);
            } catch (\Throwable $exception) {
                $trans[] = $this->tagTranslationRepository->getForLocalization($tag, $this->defaultLocale);
            }
        }

        $searchCriteria[$transName] = implode('+', array_column($trans, 'slug'));
    }
}
