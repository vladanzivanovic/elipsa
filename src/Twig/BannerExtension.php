<?php

declare(strict_types=1);

namespace App\Twig;

use App\Collector\CartPageCollector;
use App\Entity\BannerTranslation;
use App\Formatter\Site\CartPageFormatter;
use App\Repository\BannerRepository;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class BannerExtension extends AbstractExtension
{
    /**
     * @var CartPageFormatter
     */
    private $pageFormatter;

    /**
     * @var CartPageCollector
     */
    private $pageCollector;
    /**
     * @var BannerRepository
     */
    private $bannerRepository;
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param CartPageCollector $pageCollector
     * @param CartPageFormatter $pageFormatter
     * @param BannerRepository  $bannerRepository
     * @param RouterInterface   $router
     */
    public function __construct(
        CartPageCollector $pageCollector,
        CartPageFormatter $pageFormatter,
        BannerRepository $bannerRepository,
        RouterInterface $router
    ) {
        $this->pageFormatter = $pageFormatter;
        $this->pageCollector = $pageCollector;
        $this->bannerRepository = $bannerRepository;
        $this->router = $router;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('banner', [$this, 'getBanner']),
        ];
    }

    /**
     * @param int    $type
     * @param string $filter
     * @param string $locale
     *
     * @return array|null
     */
    public function getBanner(int $type, string $filter, string $locale): ?array
    {
        $banner = $this->bannerRepository->findOneBy(['type' => $type, 'isActive' => true]);

        if (null === $banner) {
            return null;
        }
        $trans = $banner->getByLocale($locale);

        return [
            'image_link' => $this->router->generate('app.image_show', ['entity' => 'banner', 'name' => $banner->getImage()->getOriginalName(), 'filter' => $filter]),
            'image' => ['entity' => 'banner', 'name' => $banner->getImage()->getOriginalName(), 'filter' => $filter],
            'link' => $trans->getButtonLink(),
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'banner_extension';
    }
}
