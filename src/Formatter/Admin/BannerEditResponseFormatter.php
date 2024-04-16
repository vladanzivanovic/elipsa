<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Banner;
use App\View\BannerView;
use Doctrine\ORM\NonUniqueResultException;

final class BannerEditResponseFormatter
{
    public function __construct(
        private readonly BannerView $bannerView
    ) {}


    /**
     * @throws NonUniqueResultException
     */
    public function formatResponse(Banner $banner): array
    {
        $view = $this->bannerView->editView($banner);

        return [
            'payload' => $view,
        ];
    }
}
