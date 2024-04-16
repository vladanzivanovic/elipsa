<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Formatter\Admin\ImageTrait;
use App\Formatter\Site\CatalogueResponseFormatter;
use App\Repository\CatalogueRepository;
use App\Repository\ImageRepository;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

final class CatalogPageController extends AbstractController
{
    use ImageTrait;

    private \App\Repository\ImageRepository $imageRepository;
    private \Symfony\Component\Routing\RouterInterface $router;
    private \App\Repository\CatalogueRepository $catalogueRepository;
    private \App\Formatter\Site\CatalogueResponseFormatter $responseFormatter;

    public function __construct(
        ImageRepository $imageRepository,
        RouterInterface $router,
        CatalogueRepository $catalogueRepository,
        CatalogueResponseFormatter $responseFormatter
    ) {
        $this->imageRepository = $imageRepository;
        $this->router = $router;
        $this->catalogueRepository = $catalogueRepository;
        $this->responseFormatter = $responseFormatter;
    }

    
    #[Route(path: ['rs' => '/katalog', 'en' => '/catalogue', 'ba' => '/katalog'], name: 'site.catalog_page', methods: ['GET'])]
    #[Template('Site/Pages/catalog.html.twig')]
    public function getCatalogues(Request $request): array
    {
        $locale = $request->getLocale();

        $catalogues = $this->catalogueRepository->getCatalogPage($locale);

        return $this->responseFormatter->formatResponse($catalogues, $locale);
    }
}
