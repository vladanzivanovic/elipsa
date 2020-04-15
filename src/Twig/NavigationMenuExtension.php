<?php

declare(strict_types=1);

namespace App\Twig;

use App\Repository\CategoryRepository;
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
     * NavigationMenuExtension constructor.
     *
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        CategoryRepository $categoryRepository
    ) {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('navigation_menu', [$this, 'getNavigationMenu']),
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
