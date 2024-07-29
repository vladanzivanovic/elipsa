<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Formatter\Admin\ProductSearchResponseFormatter;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductListSearchController extends AbstractController
{
    private ProductRepository $productRepository;

    private ProductSearchResponseFormatter $productSearchResponseFormatter;

    public function __construct(
        ProductRepository $productRepository,
        ProductSearchResponseFormatter $productSearchResponseFormatter
    ) {
        $this->productRepository = $productRepository;
        $this->productSearchResponseFormatter = $productSearchResponseFormatter;
    }

    #[Route(path: '/api/products/search', name: 'admin.product_search_name_api', methods: ['GET'])]
    public function getProductList(Request $request): Response
    {
        $products = $this->productRepository->getProductsByQueryString($request->query->get('query'));

        return $this->json(
            $this->productSearchResponseFormatter->format($products),
            Response::HTTP_OK
        );
    }
}
