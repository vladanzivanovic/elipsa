<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Banner;
use App\Formatter\Admin\BannerEditResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;

final class BannerEditPageController extends AbstractController
{
    /**
     * @var BannerEditResponseFormatter
     */
    private $responseFormatter;
    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * @param BannerEditResponseFormatter $responseFormatter
     * @param ParameterBagInterface       $bag
     */
    public function __construct(
        BannerEditResponseFormatter $responseFormatter,
        ParameterBagInterface $bag
    ) {
        $this->responseFormatter = $responseFormatter;
        $this->bag = $bag;
    }

    /**
     * @Route("/add-home-banner", name="admin.add_home_banner_page", methods={"GET"})
     * @Template("Admin/Pages/homeBannerEdit.html.twig")
     *
     * @return array
     */
    public function insertHome(): array
    {
        return [];
    }

    /**
     * @Route("/edit-home-banner/{id}", name="admin.edit_home_banner_page", methods={"GET"})
     * @Template("Admin/Pages/homeBannerEdit.html.twig")
     *
     * @param Banner $banner
     *
     * @return array
     */
    public function updateHome(Banner $banner): array
    {
        return $this->responseFormatter->formatResponse($banner);
    }

    /**
     * @Route("/add-banner", name="admin.add_banner_page", methods={"GET"})
     * @Template("Admin/Pages/bannerEdit.html.twig")
     *
     * @return array
     */
    public function insert(): array
    {
        return [];
    }

    /**
     * @Route("/edit-banner/{id}", name="admin.edit_banner_page", methods={"GET"})
     * @Template("Admin/Pages/bannerEdit.html.twig")
     *
     * @param Banner $banner
     *
     * @return array
     */
    public function update(Banner $banner): array
    {
        return $this->responseFormatter->formatResponse($banner);
    }
}