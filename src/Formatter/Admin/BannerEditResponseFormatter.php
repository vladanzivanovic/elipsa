<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Banner;
use Symfony\Component\Routing\RouterInterface;

final class BannerEditResponseFormatter
{
    use ImageTrait;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * BannerEditResponseFormatter constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(
        RouterInterface $router
    ) {
        $this->router = $router;
    }

    /**
     * @param Banner $banner
     *
     * @return array
     */
    public function formatResponse(Banner $banner): array
    {
        $rsTrans = $banner->getByLocale('rs');
        $enTrans = $banner->getByLocale('en');

        return [
            'rs_description' => $rsTrans->getDescription(),
            'rs_button' => $rsTrans->getButtonText(),
            'rs_link' => $rsTrans->getButtonLink(),
            'en_description' => $enTrans->getDescription(),
            'en_button' => $enTrans->getButtonText(),
            'en_link' => $enTrans->getButtonLink(),
            'position' => $banner->getPosition(),
            'selectedImages' => $this->imagesFormatter($this->router, [$this->getImage($banner)], 'banner'),
            'type' => $banner->getType(),
        ];
    }

    /**
     * @param Banner $banner
     *
     * @return array
     */
    private function getImage(Banner $banner): array
    {
        $image = $banner->getImage();

        return [
            'id' => $image->getId(),
            'fileName' => $image->getName(),
            'isMain' => $image->getIsMain(),
        ];
    }
}