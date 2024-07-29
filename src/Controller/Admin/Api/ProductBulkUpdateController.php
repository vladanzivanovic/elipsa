<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Repository\ProductOptionsRepository;
use App\Repository\ProductRepository;
use App\Request\Dto\ProductsBulkRequestDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductBulkUpdateController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly ProductOptionsRepository $productOptionsRepository,
    ) {}

    #[Route(path: '/api/products/bulk/home-page-position/{position}/{country}', name: 'admin.api_bulk_product_home_page_position', options: ['expose' => true], methods: ['POST'])]
    public function setBulkHomePagePosition(Request $request, string $position, string $country): JsonResponse
    {
        $body = json_decode($request->getContent(), true);

        $products = $this->productRepository->findBy(['id' => $body['ids']]);

        $highestPositionResult = $this->productOptionsRepository->getHighestHomePagePosition($position, $country);
        $highestPosition = $highestPositionResult['showHomePage'][$position] ?? 0;

        foreach ($products as $index => $product) {
            $option = $product->getOptionsByCountry($country);

            if (null === $option) {
                continue;
            }

            if ('0' === $position) {
                $option->setShowHomePage(null);

                continue;
            }

            $index++;

            $showHomePage = $option->getShowHomePage() ?? [];

            $showHomePage[$position] = $index + $highestPosition;

            $option->setShowHomePage($showHomePage);
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

    #[Route(path: '/api/products/bulk/home-page-position', name: 'admin.api_bulk_products_home_page_position', options: ['expose' => true], methods: ['POST'])]
    public function setBulkProductsHopePagePositions(ProductsBulkRequestDto $bulkRequestDto): JsonResponse
    {
        foreach ($bulkRequestDto->products as $position => $productsByPosition) {
            foreach ($productsByPosition as $productOptionsId) {
                foreach ($productOptionsId as $index => $optionId) {
                    $productOption = $this->productOptionsRepository->find($optionId);

                    $homePagePosition = $productOption->getShowHomePage();

                    $homePagePosition[$position] = $index + 1;

                    $productOption->setShowHomePage($homePagePosition);
                }
            }
        }

        $this->productRepository->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
