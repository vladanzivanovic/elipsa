<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Product;
use App\Entity\ProductTranslation;
use App\Handler\ProductEditHandler;
use App\Helper\ConstantsHelper;
use App\Parser\ProductEditRequestParser;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/api/product/add", name="admin.add_product_api", methods={"POST"}, options={"expose": true})
     * @throws ORMException
     */
    public function insert(Request $request): JsonResponse
    {
        $product = $this->requestParser->parse($request->request);

        $this->editHandler->save($product);

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/product/{slug}", name="admin.edit_product_api", methods={"PUT"}, options={"expose": true})
     * @throws ORMException
     */
    public function update(Request $request, ProductTranslation $productTranslation): JsonResponse
    {
        $product = $this->requestParser->parse($request->request, $productTranslation->getProduct());

        $this->editHandler->save($product);

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/product/status/{slug}/{status}", name="admin.api_product_change_status", methods={"PATCH"}, options={"expose": true})
     */
    public function changeStatus(ProductTranslation $productTranslation, int $status): JsonResponse
    {
        $productTranslation->getProduct()->setStatus($status);

        $this->editHandler->save($productTranslation->getProduct());

        $statusText = ConstantsHelper::getConstantName((string) $status, 'STATUS', Product::class);

        return $this->json(['text' => $statusText]);
    }

    /**
     * @Route("/api/product/home-page-position/{slug}/{status}",
     *     name="admin.api_product_home_page_position",
     *     methods={"PATCH"},
     *     options={"expose": true}
     * )
     */
    public function setHomePagePosition(ProductTranslation $productTranslation, int $status): JsonResponse
    {
        $productTranslation->getProduct()->setShowHomePage($status);

        $this->editHandler->save($productTranslation->getProduct());

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/api/product/sold/{slug}",
     *     name="admin.api_product_is_sold",
     *     methods={"PATCH"},
     *     options={"expose": true}
     * )
     */
    public function toggleProductIsSold(ProductTranslation $productTranslation): JsonResponse
    {
        $product = $productTranslation->getProduct();
        $isSold = $product->isSold();

        $product->setSold(!$isSold);

        $this->editHandler->save($productTranslation->getProduct());

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
