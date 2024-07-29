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
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;

final class CatalogPageController extends AbstractController
{
    use ImageTrait;

    public function __construct(
        private readonly ImageRepository $imageRepository,
        private readonly RouterInterface $router,
        private readonly CatalogueRepository $catalogueRepository,
        private readonly CatalogueResponseFormatter $responseFormatter
    ) {}

    
    #[Route(path: [
        'rs' => '/katalog',
        'en' => '/catalogue',
        'ba' => '/katalog'
    ], name: 'site.catalog_page', methods: ['GET'])]
    #[Template('Site/Pages/catalog.html.twig')]
    public function getCatalogues(Request $request): array
    {
        $locale = $request->getLocale();
        $countryCode = $request->attributes->get('_country');

        $catalogues = $this->catalogueRepository->getCatalogPage($locale, $countryCode);

        return $this->responseFormatter->formatResponse($catalogues, $locale);
    }
}
