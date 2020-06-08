<?php

declare(strict_types=1);

namespace App\Formatter\Site\Router;

use App\Repository\CategoryTranslationRepository;
use App\Repository\ColorTranslationRepository;
use App\Repository\TagsRepository;
use App\ShopTrait;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ShopPageRouterFormatter
{
    use ShopTrait;

    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var TagsRepository
     */
    private $tagsRepository;
    /**
     * @var ColorTranslationRepository
     */
    private $colorTranslationRepository;
    /**
     * @var CategoryTranslationRepository
     */
    private $categoryTranslationRepository;

    /**
     * @param ParameterBagInterface         $bag
     * @param TranslatorInterface           $translator
     * @param TagsRepository                $tagsRepository
     * @param ColorTranslationRepository    $colorTranslationRepository
     * @param CategoryTranslationRepository $categoryTranslationRepository
     */
    public function __construct(
        ParameterBagInterface $bag,
        TranslatorInterface $translator,
        TagsRepository $tagsRepository,
        ColorTranslationRepository $colorTranslationRepository,
        CategoryTranslationRepository $categoryTranslationRepository
    ) {
        $this->tagsRepository = $tagsRepository;
        $this->bag = $bag;
        $this->translator = $translator;
        $this->colorTranslationRepository = $colorTranslationRepository;
        $this->categoryTranslationRepository = $categoryTranslationRepository;
    }

    public function localeFormatter(string $searchData, string $locale)
    {
        $searchData = $this->parseSearchData($searchData);

        return $this->createUrlString($searchData, $locale);
    }
    /**
     * @param ParameterBag $bag
     * @param string       $locale
     *
     * @return string
     */
    public function createUrlString(ParameterBag $bag, string $locale): string
    {
        $urlParams = '';

        foreach($bag->all() as $filter => $criteria) {
            $criteriaTrans = [];

            switch ($filter) {
                case 'tags':
                    foreach ($criteria as $tag) {
                        $criteriaTrans[] = $this->tagsRepository->getForLocalization($tag, $locale);
                    }
                    break;
                case 'color':
                    foreach ($criteria as $color) {
                        $criteriaTrans[] = $this->colorTranslationRepository->getForLocalization($color, $locale);
                    }
                    break;
                case 'categories':
                    foreach ($criteria as $category) {
                        $criteriaTrans[] = $this->categoryTranslationRepository->getForLocalization($category, $locale);
                    }
                    break;
                case 'sort':
                    $sortMapping = $this->bag->get('shop')['sort_mapping'];

                    $criteriaTrans[] = array_search($criteria, $sortMapping);
                    break;
                default:
                    $criteriaTrans = $criteria;
            }

            $mergedCriteria = implode('+', $criteriaTrans);

            $filterTrans = $this->translator->trans($filter, [], 'messages', $locale);

            $urlParams .= '/'.$filterTrans.'/'.$mergedCriteria;
        }

        return substr($urlParams, 1);
    }
}