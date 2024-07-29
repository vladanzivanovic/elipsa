<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Entity\CatalogueTranslation;
use App\View\CatalogView;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CatalogDetailPageController extends AbstractController
{
    private CatalogView $catalogView;

    public function __construct(
        CatalogView $catalogView
    ) {
        $this->catalogView = $catalogView;
    }

    //todo try to customise error message from EntityValueResolver
    #[Route(path: [
        'rs' => '/katalog/{slug}',
        'en' => '/catalogue/{slug}',
        'ba' => '/katalog/{slug}'
    ], name: 'site.catalog_detail_page', methods: ['GET'])]
    #[Template('Site/Pages/catalogDetail.html.twig')]
    public function getCatalog(
        #[MapEntity(expr: 'repository.getByCountryCode(slug, request.attributes.get("_country"))')]
        CatalogueTranslation $catalogueTranslation,
        Request $request
    ): array {

        return $this->catalogView->view(
            $catalogueTranslation->getCatalogue(),
            $request->getLocale()
        );
    }
}
