<?php

declare(strict_types=1);

namespace App\Formatter\Site\Router;

use App\Entity\Tags;
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

    private ParameterBagInterface $bag;

    private TranslatorInterface $translator;

    private ColorTranslationRepository $colorTranslationRepository;

    private CategoryTranslationRepository $categoryTranslationRepository;

    private TagUrlLocalizationFormatter $tagUrlLocalizationFormatter;

    private TagTranslationRepository $tagTranslationRepository;

    private array $locales;

    private RouterInterface $router;

    public function __construct(
        ParameterBagInterface $bag,
        TranslatorInterface $translator,
        ColorTranslationRepository $colorTranslationRepository,
        CategoryTranslationRepository $categoryTranslationRepository,
        TagUrlLocalizationFormatter $tagUrlLocalizationFormatter,
        TagTranslationRepository $tagTranslationRepository,
        RouterInterface $router,
        string $locales
    ) {
        $this->bag = $bag;
        $this->translator = $translator;
        $this->colorTranslationRepository = $colorTranslationRepository;
        $this->categoryTranslationRepository = $categoryTranslationRepository;
        $this->tagUrlLocalizationFormatter = $tagUrlLocalizationFormatter;
        $this->locales = explode('|', $locales);
        $this->tagTranslationRepository = $tagTranslationRepository;
        $this->router = $router;
    }

    public function localeFormatter(string $searchData, string $locale): string
    {
//        $searchData = $this->parseSearchData($searchData);
//
//        return $this->createUrlString($searchData, $locale);
    }

    public function createLocalizedLinks(
        ShopPageOptionsDto $shopPageOptionsDto,
        ShopListRequestDto $shopListRequestDto,
        string $routerName
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
            $this->getPrices($searchCriteria, $shopListRequestDto->price, $locale);
            $this->getSearch($searchCriteria, $shopListRequestDto->search, $locale);
            $this->getSort($searchCriteria, $shopPageOptionsDto->sort, $locale);
            $this->getLimit($searchCriteria, $shopPageOptionsDto->limit, $locale);

            $urlParams = [
                '_locale' => $locale,
                'page' => $shopPageOptionsDto->page,
            ];

            $translations[$locale] = $this->router->generate(
                $routerName,
                $urlParams + $searchCriteria,
                UrlGeneratorInterface::ABSOLUTE_URL
            );


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

        $transColors = [];

        foreach ($colors as $color) {
            $transColors[] = $this->colorTranslationRepository->getForLocalization($color, $locale);
        }

        $searchCriteria[$transName] = implode('+', $transColors);
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
            $trans[] = $this->categoryTranslationRepository->getForLocalization($category, $locale);
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
            $trans[] = $this->tagTranslationRepository->getForLocalization($tag, $locale);
        }

        $searchCriteria[$transName] = implode('+', $trans);
    }
}
