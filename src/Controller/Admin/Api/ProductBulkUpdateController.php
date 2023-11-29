<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Repository\ProductRepository;
use App\Request\Dto\ProductsBulkRequestDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductBulkUpdateController extends AbstractController
{
    private ProductRepository $productRepository;

    public function __construct(
        ProductRepository $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/api/products/bulk/home-page-position/{position}",
     *     name="admin.api_bulk_product_home_page_position",
     *     methods={"POST"},
     *     options={"expose": true}
     * )
     */
    public function setBulkHomePagePosition(Request $request, int $position): JsonResponse
    {
        $body = json_decode($request->getContent(), true);

        $products = $this->productRepository->findBy(['id' => $body['ids']]);

        foreach ($products as $product) {
            $product->setShowHomePage($position);
        }

        $this->productRepository->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/api/products/bulk/discount",
     *     name="admin.api_bulk_products_discount",
     *     methods={"POST"},
     *     options={"expose": true}
     * )
     */
    public function setBulkProductsDiscount(ProductsBulkRequestDto $bulkRequestDto): JsonResponse
    {
        $products = $this->productRepository->findBy(['id' => $bulkRequestDto->products]);

        foreach ($products as $product) {
            $discountAmount = $product->getPrice() * ((100 - $bulkRequestDto->discount)/100);

            $product->setDiscount((int) $discountAmount);
        }

        $this->productRepository->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
