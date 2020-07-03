<?php

declare(strict_types=1);

namespace App\Twig;

use App\Repository\CategoryRepository;
use App\Repository\SliderTextRepository;
use App\Repository\TagsRepository;
use DateTime;
use Exception;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class NavigationMenuExtension extends AbstractExtension
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var TagsRepository
     */
    private $tagsRepository;

    /**
     * @var SliderTextRepository
     */
    private $sliderTextRepository;

    /**
     * @param CategoryRepository   $categoryRepository
     * @param TagsRepository       $tagsRepository
     * @param SliderTextRepository $sliderTextRepository
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        TagsRepository $tagsRepository,
        SliderTextRepository $sliderTextRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->tagsRepository = $tagsRepository;
        $this->sliderTextRepository = $sliderTextRepository;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('navigation_menu', [$this, 'getNavigationMenu']),
            new TwigFunction('navigation_tags', [$this, 'getNavigationTags']),
            new TwigFunction('slider_text', [$this, 'getSliderText']),
        ];
    }

    /**
     * @param string $locale
     *
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getNavigationMenu(string $locale): array
    {
        $categories = $this->categoryRepository->getForNavigationMenu($locale);

        $lastCategory = end($categories);

        $categories = $this->formatMegaMenu($categories, 1, (int) $lastCategory['lvl']);

        return array_filter($categories, function ($category) {
            return null === $category['parent_id'];
        });
    }

    /**
     * @param string $locale
     *
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getNavigationTags(string $locale): array
    {
        $tags = $this->tagsRepository->getForNavigationMenu($locale);

        return $tags;
    }

    /**
     * @param string $locale
     *
     * @return array
     */
    public function getSliderText(string $locale): array
    {
        return $this->sliderTextRepository->getList($locale);
    }

    private function formatMegaMenu(array $categories, int $level, int $maxLevel)
    {
        $formattedMenu = [];

        foreach ($categories as $category) {
            $childrenCategories = array_filter($categories, function ($cat) use ($category) {
                return $cat['parent_id'] === $category['id'];
            });

            if (count($childrenCategories) > 0) {
                $category['children'] = $childrenCategories;
            }

            $formattedMenu[] = $category;
        }

        if ($level <= $maxLevel) {
            return self::formatMegaMenu($formattedMenu, $level + 1, $maxLevel);
        }

        return $formattedMenu;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'navigation_extension';
    }
}
