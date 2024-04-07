<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Image;
use App\Repository\BannerRepository;
use App\Repository\ImageRepository;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class BannerExtension extends AbstractExtension
{
    public function __construct(
        private BannerRepository $bannerRepository,
        private RouterInterface $router,
        private ImageRepository $imageRepository,
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('banner', [$this, 'getBanner']),
        ];
    }

    public function getBanner(int $type, string $filter, string $locale, bool $isMobile = false): array|null
    {
        $banner = $this->bannerRepository->findOneBy(['type' => $type, 'isActive' => true]);

        if (null === $banner) {
            return null;
        }

        $imageName = $banner->getImage()->getName();

        $trans = $banner->getByLocale($locale);

        if ($isMobile) {
            $mobileImage = $this->imageRepository->findOneBy(['parentImage' => $banner->getImage()->getName(), 'device' => Image::DEVICE_MOBILE]);

            if (null !== $mobileImage) {
                $imageName = $mobileImage->getName();
            }
        }

        return [
            'image_link' => $this->router->generate('app.image_show', ['entity' => 'banner', 'name' => $imageName, 'filter' => $filter]),
            'image' => ['entity' => 'banner', 'name' => $imageName, 'filter' => $filter],
            'link' => $trans->getButtonLink(),
        ];
    }

    public function getName(): string
    {
        return 'banner_extension';
    }
}
