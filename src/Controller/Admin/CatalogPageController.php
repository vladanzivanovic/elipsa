<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Banner;
use App\Formatter\Admin\BannerEditResponseFormatter;
use App\Formatter\Admin\CatalogResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;

final class CatalogPageController extends AbstractController
{
    /**
     * @var CatalogResponseFormatter
     */
    private $responseFormatter;
    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * @param CatalogResponseFormatter $responseFormatter
     * @param ParameterBagInterface    $bag
     */
    public function __construct(
        CatalogResponseFormatter $responseFormatter,
        ParameterBagInterface $bag
    ) {
        $this->responseFormatter = $responseFormatter;
        $this->bag = $bag;
    }

    /**
     * @Route("/catalog", name="admin.catalog_page", methods={"GET"})
     * @Template("Admin/Pages/catalog.html.twig")
     *
     * @return array
     */
    public function set(): array
    {
        return $this->responseFormatter->formatResponse();
    }
}