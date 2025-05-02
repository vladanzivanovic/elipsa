<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Entity\Banner;
use App\Entity\ProductOptions;
use App\Entity\Promotion;
use App\Entity\PromotionOption;
use App\Entity\Tags;
use App\Entity\User;
use App\View\BannerView;
use App\View\SliderView;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class HomePageResponseFormatter
{
    use FormatterTrait;

    public function __construct(
        private readonly RouterInterface $router,
        private readonly SliderView $sliderView,
        private readonly BannerView $bannerView,
        private readonly ProductFormatter $productFormatter,
        private readonly TranslatorInterface $translator,
    ) {}

    public function formatResponse(array $data, string $locale, string $countryCode, null|User $user = null): array
    {
        $data['sliders'] = array_map(function ($slider) use ($locale) {

            return $this->sliderView->siteView($slider, $locale);
        }, $data['sliders']);

        if (null !== $data['banners'][Banner::TYPE_SPEED_LINKS]) {
            $data['banners'][Banner::TYPE_SPEED_LINKS] = $this->formatSpeedLinksBanners($data['banners'][Banner::TYPE_SPEED_LINKS]);
        }

        if (0 < count($data['banners'][Banner::TYPE_LOYALTY])) {
            $data['banners'][Banner::TYPE_LOYALTY] = $this->createBannerView(
                $data['banners'][Banner::TYPE_LOYALTY][0],
                ['desktop' => 'loyalty_banner', 'mobile' => 'loyalty_banner_mobile'],
            );
        }

        $data['products'][ProductOptions::HOME_PAGE_UP] = $this->productFormatter->getProducts($data['products'][ProductOptions::HOME_PAGE_UP], $countryCode, $user);
        $data['products'][ProductOptions::HOME_PAGE_DOWN] = $this->productFormatter->getProducts($data['products'][ProductOptions::HOME_PAGE_DOWN], $countryCode, $user);

        $data['promotion'] = $this->setPromotionData($data['promotion']);

        return $data;
    }

    /**
     * @param Banner[] $banners
     */
    private function formatSpeedLinksBanners(array $banners): array
    {
        $formattedBanners = [];

        foreach ($banners as $banner) {
            $filter = in_array($banner->getPosition(), [1,4]) ? 'home_banner_side' : 'home_banner_center';

            $formattedBanners[$banner->getPosition()] = $this->createBannerView($banner, ['desktop' => $filter, 'mobile' => $filter]);
        }

        return $formattedBanners;
    }

    private function createBannerView(Banner $banner, array $filters): array
    {
        return $this->bannerView->view($banner, $filters);
    }

    private function setPromotionData(PromotionOption|null $promotionOption): array|null
    {
        if (null === $promotionOption) {
            return null;
        }

        $promotion = $promotionOption->getPromotionId();

        return [
            'valid_to' => $promotion->getValidTo()->format('Y/m/d H:i:s'),
            'translations' => $this->generatePromotionTagTranslations($promotion->getTags()),
            '_links' => $this->generateLinks($promotion->getTags()),
        ];
    }

    private function generateLinks(Tags $tags): array
    {
        $linkLocales = [];

        foreach ($tags->getTagTranslations() as $tagTranslation) {

            $locale = $tagTranslation->getLocale();

            $promotionTranslationKey = $this->translator->trans('filter.promotions', [], 'messages', $locale);

            $urlParams = [
                $promotionTranslationKey => $tagTranslation->getSlug(),
                '_locale' => $locale,
            ];

            $linkLocales[$locale] = $this->router->generate(
                'site.shop_page',
                $urlParams,
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }

        return $linkLocales;
    }

    private function generatePromotionTagTranslations(Tags $tags): array
    {
        $translations = [];

        foreach ($tags->getTagTranslations() as $tagTranslation) {

            $locale = $tagTranslation->getLocale();


            $translations[$locale] = $tagTranslation->getTitle();
        }

        return $translations;
    }
}
