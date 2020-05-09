<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Banner;
use App\Formatter\Admin\BannerEditResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;

final class LocationEditPageController extends AbstractController
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
     * @Route("/add-location", name="admin.add_location_page", methods={"GET"})
     * @Template("Admin/Pages/locationEdit.html.twig")
     *
     * @return array
     */
    public function insert(): array
    {
        return [];
    }

    /**
     * @Route("/edit-location/{id}", name="admin.edit_location_page", methods={"GET"})
     * @Template("Admin/Pages/locationEdit.html.twig")
     *
     * @param Banner $banner
     *
     * @return array
     */
    public function update(Banner $banner): array
    {
        $locales = explode('|', $this->bag->get('locales'));

        return $this->responseFormatter->formatResponse($banner);
    }
}