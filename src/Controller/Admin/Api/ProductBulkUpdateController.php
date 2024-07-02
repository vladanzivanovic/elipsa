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
    public function __construct(
        private readonly ProductRepository $productRepository
    ) {}

    #[Route(path: '/api/products/bulk/home-page-position/{position}', name: 'admin.api_bulk_product_home_page_position', options: ['expose' => true], methods: ['POST'])]
    public function setBulkHomePagePosition(Request $request, string $position): JsonResponse
    {
        $body = json_decode($request->getContent(), true);

        $products = $this->productRepository->findBy(['id' => $body['ids']]);

        foreach ($products as $product) {
            $options = $product->getProductOptions();

            foreach ($options as $option) {
                $option->setShowHomePage($position === '0' ? null : $position);
            }
        }

        $this->productRepository->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route(path: '/api/products/bulk/discount', name: 'admin.api_bulk_products_discount', options: ['expose' => true], methods: ['POST'])]
    public function setBulkProductsDiscount(ProductsBulkRequestDto $bulkRequestDto): JsonResponse
    {
        $products = $this->productRepository->findBy(['id' => $bulkRequestDto->products]);

        foreach ($products as $product) {
            $options = $product->getProductOptions();
            foreach ($options as $option) {
                $discountAmount = $option->getPrice() * ((100 - $bulkRequestDto->discount)/100);

                $option->setDiscount((int) $discountAmount);
            }
        }

        $this->productRepository->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
