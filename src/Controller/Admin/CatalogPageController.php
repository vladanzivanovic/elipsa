<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Banner;
use App\Entity\Catalogue;
use App\Formatter\Admin\BannerEditResponseFormatter;
use App\Formatter\Admin\CatalogResponseFormatter;
use Symfony\Bridge\Twig\Attribute\Template;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;

final class CatalogPageController extends AbstractController
{
    private \App\Formatter\Admin\CatalogResponseFormatter $responseFormatter;

    /**
     * @param ParameterBagInterface    $bag
     */
    public function __construct(
        CatalogResponseFormatter $responseFormatter
    ) {
        $this->responseFormatter = $responseFormatter;
    }

    #[Route(path: '/add-catalog', name: 'admin.add_catalog_page', methods: ['GET'])]
    #[Template('Admin/Pages/catalogEdit.html.twig')]
    public function set(): array
    {
        return [];
    }

    
    #[Route(path: '/edit-catalog/{id}', name: 'admin.edit_catalog_page', methods: ['GET'])]
    #[Template('Admin/Pages/catalogEdit.html.twig')]
    public function update(Catalogue $catalogue): array
    {
        return $this->responseFormatter->formatResponse($catalogue);
    }
}
