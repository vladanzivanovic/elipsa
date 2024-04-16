<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Banner;
use App\Formatter\Admin\BannerEditResponseFormatter;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class BannerEditPageController extends AbstractController
{
    public function __construct(
        private readonly BannerEditResponseFormatter $responseFormatter
    ) {
    }

    #[Route(path: '/add-home-banner', name: 'admin.add_home_banner_page', methods: ['GET'])]
    #[Template('Admin/Pages/homeBannerEdit.html.twig')]
    public function insertHome(): array
    {
        return [];
    }

    
    #[Route(path: '/edit-home-banner/{id}', name: 'admin.edit_home_banner_page', methods: ['GET'])]
    #[Template('Admin/Pages/homeBannerEdit.html.twig')]
    public function updateHome(Banner $banner): array
    {
        return $this->responseFormatter->formatResponse($banner);
    }

    #[Route(path: '/add-banner', name: 'admin.add_banner_page', methods: ['GET'])]
    #[Template('Admin/Pages/bannerEdit.html.twig')]
    public function insert(): array
    {
        return [];
    }

    
    #[Route(path: '/edit-banner/{id}', name: 'admin.edit_banner_page', methods: ['GET'])]
    #[Template('Admin/Pages/bannerEdit.html.twig')]
    public function update(Banner $banner): array
    {
//        dd($this->responseFormatter->formatResponse($banner));
        return $this->responseFormatter->formatResponse($banner);
    }
}
