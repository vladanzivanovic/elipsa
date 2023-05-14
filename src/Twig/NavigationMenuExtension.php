<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Banner;
use App\Repository\BannerRepository;
use App\Repository\CategoryRepository;
use App\Repository\SliderTextRepository;
use App\Repository\TagsRepository;
use App\View\BannerView;
use App\View\SliderTextView;
use DateTime;
use Exception;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class NavigationMenuExtension extends AbstractExtension
{
    private CategoryRepository $categoryRepository;

    private TagsRepository $tagsRepository;

    private SliderTextRepository $sliderTextRepository;

    private BannerRepository $bannerRepository;

    private RouterInterface $router;

    private BannerView $bannerView;

    private SliderTextView $sliderTextView;

    public function __construct(
        CategoryRepository $categoryRepository,
        TagsRepository $tagsRepository,
        SliderTextRepository $sliderTextRepository,
        BannerRepository $bannerRepository,
        RouterInterface $router,
        BannerView $bannerView,
        SliderTextView $sliderTextView
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->tagsRepository = $tagsRepository;
        $this->sliderTextRepository = $sliderTextRepository;
        $this->bannerRepository = $bannerRepository;
        $this->router = $router;
        $this->bannerView = $bannerView;
        $this->sliderTextView = $sliderTextView;
    }

    /**
     * @return array<int, TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('navigation_menu', [$this, 'getNavigationMenu']),
            new TwigFunction('navigation_tags', [$this, 'getNavigationTags']),
            new TwigFunction('slider_text', [$this, 'getSliderText']),
            new TwigFunction('menu_banners', [$this, 'getMenuBanners']),
            new TwigFunction('season_banner', [$this, 'getSeasonBanner']),
        ];
    }

    public function getNavigationMenu(string $locale): array
    {
        $categories = $this->categoryRepository->getForNavigationMenu($locale);

        $lastCategory = end($categories);

        $categories = $this->formatMegaMenu($categories, 1, (int) $lastCategory['lvl']);

        return array_filter($categories, function ($category) {
            return null === $category['parent_id'];
        });
    }

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
        $texts = $this->sliderTextRepository->getList();

        $sliderTexts = [];

        foreach ($texts as $text) {
            $sliderTexts[] = $this->sliderTextView->siteView($text, $locale);
        }

        return $sliderTexts;
    }

    public function getMenuBanners(string $locale): array
    {
        return $this->getBanners($locale, Banner::TYPE_MENU);
    }

    public function getSeasonBanner(string $locale): ?array
    {
        $banners = $this->getBanners($locale, Banner::TYPE_SEASON);

        return $banners[0] ?? null;
    }

    private function formatMegaMenu(array $categories, int $level, int $maxLevel): array
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

    public function getName(): string
    {
        return 'navigation_extension';
    }

    public function getBanners(string $locale, int $type): array
    {
        $banners = $this->bannerRepository->getActiveByType($type);

        $formattedBanners = [];

        foreach ($banners as $banner) {
//            $banner['image_link'] = $this->router->generate('app.image_show', ['entity' => 'banner', 'name' => $banner['image'], 'filter' => 'menu_banner']);

            $formattedBanners[] = $this->bannerView->menuView($banner, $locale);
        }

        return $formattedBanners;
    }
}
