<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Banner;
use App\Entity\Tags;
use App\Repository\BannerRepository;
use App\Repository\CategoryRepository;
use App\Repository\SliderTextRepository;
use App\Repository\TagsRepository;
use App\View\BannerView;
use App\View\SliderTextView;
use App\View\TagView;
use Doctrine\DBAL\Exception;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class NavigationMenuExtension extends AbstractExtension
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly TagsRepository $tagsRepository,
        private readonly SliderTextRepository $sliderTextRepository,
        private readonly BannerRepository $bannerRepository,
        private readonly BannerView $bannerView,
        private readonly SliderTextView $sliderTextView,
        private readonly TagView $tagView,
        private readonly string $defaultLocale
    ) {}

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

    /**
     * @throws Exception
     */
    public function getNavigationMenu(string $locale): array
    {
        $categories = $this->categoryRepository->getForNavigationMenu($locale);

        if (0 === count($categories)) {
            $categories = $this->categoryRepository->getForNavigationMenu($this->defaultLocale);
        }

        $lastCategory = end($categories);

        $categories = $this->formatMegaMenu($categories, 1, (int) $lastCategory['lvl']);

        return array_filter($categories, function ($category) {
            return null === $category['parent_id'];
        });
    }

    public function getNavigationTags(string $productType): array
    {
        $tags = $this->tagsRepository->findBy(['relatedType' => Tags::TYPE_PRODUCT, 'productType' => $productType]);

        $formattedTags = [];

        foreach ($tags as $tag) {
            $formattedTags[] = $this->tagView->view($tag);
        }

        return $formattedTags;
    }

    public function getSliderText(string $locale, string $position): array
    {
        $texts = $this->sliderTextRepository->getListByPosition($position);

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

            if ($childrenCategories !== []) {
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
        $banners = $this->bannerRepository->getActiveByType($type, $locale);

        $formattedBanners = [];

        foreach ($banners as $banner) {
            $formattedBanners[] = $this->bannerView->view($banner);
        }

//        dd($formattedBanners);

        return $formattedBanners;
    }
}
