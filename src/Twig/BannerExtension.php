<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Image;
use App\Repository\BannerRepository;
use App\Repository\ImageRepository;
use App\View\BannerView;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class BannerExtension extends AbstractExtension
{
    public function __construct(
        private readonly BannerRepository $bannerRepository,
        private readonly RouterInterface $router,
        private readonly ImageRepository $imageRepository,
        private readonly BannerView $bannerView,
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_banners', [$this, 'getBanner']),
        ];
    }

    public function getBanner(int $type, array $filters, string $host): array
    {
        $banners = $this->bannerRepository->getActiveByType($type, $host);

        $formattedBanners = [];

        foreach ($banners as $banner) {
            $formattedBanners[] = $this->bannerView->view($banner, $filters);
        }

        return  $formattedBanners;
//
//        $imageName = $banner->getImage()->getName();
//
//        $trans = $banner->getByLocale($locale);
//
//        if ($isMobile) {
//            $mobileImage = $this->imageRepository->findOneBy(['parentImage' => $banner->getImage()->getName(), 'device' => Image::DEVICE_MOBILE]);
//
//            if (null !== $mobileImage) {
//                $imageName = $mobileImage->getName();
//            }
//        }
//
//        return [
//            'image_link' => $this->router->generate('app.image_show', ['entity' => 'banner', 'name' => $imageName, 'filter' => $filter]),
//            'image' => ['entity' => 'banner', 'name' => $imageName, 'filter' => $filter],
//            'link' => $trans->getButtonLink(),
//        ];
    }

    public function getName(): string
    {
        return 'banner_extension';
    }
}
