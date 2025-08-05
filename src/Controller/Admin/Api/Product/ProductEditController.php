<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api\Product;

use App\Entity\Product;
use App\Entity\ProductTranslation;
use App\Handler\ProductEditHandler;
use App\Helper\ConstantsHelper;
use App\Parser\ProductEditRequestParser;
use App\Request\Dto\Admin\ProductEditRequestDto;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductEditController extends AbstractController
{
    private ProductEditRequestParser $requestParser;

    private ProductEditHandler $editHandler;

    public function __construct(
        ProductEditRequestParser $requestParser,
        ProductEditHandler $editHandler
    ) {
        $this->requestParser = $requestParser;
        $this->editHandler = $editHandler;
    }

    /**
     * @throws ORMException
     */
    #[Route(path: '/api/product/add', name: 'admin.add_product_api', options: ['expose' => true], methods: ['POST'])]
    public function insert(ProductEditRequestDto $productEditRequestDto): JsonResponse
    {
        $product = $this->requestParser->parse($productEditRequestDto);

        $this->editHandler->save($product);

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     * @throws ORMException
     */
    #[Route(path: '/api/product/{slug}', name: 'admin.edit_product_api', options: ['expose' => true], methods: ['PUT'])]
    public function update(ProductEditRequestDto $productEditRequestDto, ProductTranslation $productTranslation): JsonResponse
    {
        $product = $this->requestParser->parse($productEditRequestDto, $productTranslation->getProduct());

        $this->editHandler->save($product);

        return $this->json(null, Response::HTTP_CREATED);
    }

    #[Route(path: '/api/product/status/{slug}/{status}', name: 'admin.api_product_change_status', options: ['expose' => true], methods: ['PATCH'])]
    public function changeStatus(ProductTranslation $productTranslation, int $status): JsonResponse
    {
        $productTranslation->getProduct()->setStatus($status);

        $this->editHandler->save($productTranslation->getProduct());

        $statusText = ConstantsHelper::getConstantName((string) $status, 'STATUS', Product::class);

        return $this->json(['text' => $statusText]);
    }

    #[Route(path: '/api/product/home-page-position/{country}', name: 'admin.api_product_home_page_position', options: ['expose' => true], methods: ['PATCH'])]
    public function setHomePagePosition(Product $product, string $country, Request $request): JsonResponse
    {
        $position = $request->request->all('position');

        $option = $product->getOptionByCountry($country);

        $option->setShowHomePage($position);

        $this->editHandler->save($product);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route(path: '/api/product/sold/{slug}', name: 'admin.api_product_is_sold', options: ['expose' => true], methods: ['PATCH'])]
    public function toggleProductIsSold(ProductTranslation $productTranslation): JsonResponse
    {
        $product = $productTranslation->getProduct();

        foreach ($product->getProductOptions() as $productOption) {
            $isSold = $productOption->isSold();

            $productOption->setSold(!$isSold);
        }

        $this->editHandler->save($productTranslation->getProduct());

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
