<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Banner;
use Symfony\Component\Routing\RouterInterface;

final class BannerView
{
    private RouterInterface $router;

    public function __construct(
        RouterInterface $router
    ) {
        $this->router = $router;
    }

    public function speedLinks(Banner $banner, string $locale): array
    {
        $trans = $banner->getByLocale($locale);
        $filter = in_array($banner->getPosition(), [1,4]) ? 'home_banner_side' : 'home_banner_center';

        $bannerView = $this->links($banner, $locale, $filter);

        $bannerView['description'] = explode(PHP_EOL, $trans->getDescription());

        return $bannerView;
    }

    public function menuView(Banner $banner, string $locale): array
    {
        $banner->getByLocale($locale);

        return $this->links($banner, $locale, 'menu_banner');
    }

    private function links(Banner $banner, string $locale, string $filter): array
    {
        $trans = $banner->getByLocale($locale);
        $imageName = $banner->getImage()->getName();

        return [
            'buttonLink' => $trans->getButtonLink(),
            'imageLink' => $this->router->generate(
                'app.image_show',
                ['entity' => 'banner', 'name' => $imageName, 'filter' => $filter])
        ];
    }
}
