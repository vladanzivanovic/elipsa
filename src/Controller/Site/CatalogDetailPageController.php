<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Entity\CatalogueTranslation;
use App\View\CatalogView;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CatalogDetailPageController extends AbstractController
{
    private CatalogView $catalogView;

    public function __construct(
        CatalogView $catalogView
    ) {
        $this->catalogView = $catalogView;
    }

    /**
     * @Route({
     *          "rs": "/katalog/{slug}",
     *          "en": "/catalogue/{slug}"
     *     },
     *     name="site.catalog_detail_page",
     *     methods={"GET"}
     * )
     * @Template("Site/Pages/catalogDetail.html.twig")
     *
     * @param CatalogueTranslation $catalogueTranslation
     * @param Request $request
     * @return array
     */
    public function getCatalog(
        CatalogueTranslation $catalogueTranslation,
        Request $request
    ): array {

        return $this->catalogView->view($catalogueTranslation->getCatalogue(), $request->getLocale());
    }
}
