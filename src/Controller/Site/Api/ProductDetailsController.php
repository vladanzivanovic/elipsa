<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Collector\ProductPageCollector;
use App\Entity\ProductTranslation;
use App\Formatter\Site\ProductFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductDetailsController extends AbstractController
{
    private ProductPageCollector $pageCollector;

    private ProductFormatter $pageFormatter;

    public function __construct(
        ProductPageCollector $pageCollector,
        ProductFormatter $pageFormatter
    ) {
        $this->pageCollector = $pageCollector;
        $this->pageFormatter = $pageFormatter;
    }

    #[Route(path: '/api/products/{slug}', name: 'site_api.product', options: ['expose' => true], methods: ['GET'])]
    public function index(ProductTranslation $productTranslation, Request $request): Response
    {
        $locale = $request->getLocale();

        $collection = $this->pageCollector->collect($productTranslation, $locale, $request->attributes->get('_country'), $this->getUser());

        return $this->json(
            $this->pageFormatter->formatApiResponse($collection, $locale, $this->getUser()),
            Response::HTTP_OK
        );
    }
}
