<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Product;
use App\Entity\ProductTranslation;
use App\Handler\ProductEditHandler;
use App\Helper\ConstantsHelper;
use App\Parser\ProductEditRequestParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class ProductEditController extends AbstractController
{
    /**
     * @var ProductEditRequestParser
     */
    private $requestParser;
    /**
     * @var ProductEditHandler
     */
    private $editHandler;

    /**
     * ProductEditController constructor.
     *
     * @param ProductEditRequestParser $requestParser
     * @param ProductEditHandler       $editHandler
     */
    public function __construct(
        ProductEditRequestParser $requestParser,
        ProductEditHandler $editHandler
    ) {
        $this->requestParser = $requestParser;
        $this->editHandler = $editHandler;
    }

    /**
     * @Route("/api/add-product", name="admin.add_product_api", methods={"POST"}, options={"expose": true})
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     */
    public function insert(Request $request): JsonResponse
    {
        $product = $this->requestParser->parse($request->request);

        $this->editHandler->save($product);

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/edit-product/{slug}", name="admin.edit_product_api", methods={"PUT"}, options={"expose": true})
     * @param Request            $request
     *
     * @param ProductTranslation $productTranslation
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(Request $request, ProductTranslation $productTranslation): JsonResponse
    {
        $product = $this->requestParser->parse($request->request, $productTranslation->getProduct());

        $this->editHandler->save($product);

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/product-change-status/{slug}/{status}", name="admin.api_product_change_status", methods={"PATCH"}, options={"expose": true})
     * @param ProductTranslation $productTranslation
     * @param int                $status
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public function changeStatus(ProductTranslation $productTranslation, int $status): JsonResponse
    {
        $this->editHandler->changeStatus($productTranslation->getProduct(), $status);

        $statusText = ConstantsHelper::getConstantName((string) $status, 'STATUS', Product::class);

        return $this->json(['text' => $statusText]);
    }

    /**
     * @Route("/api/product-change-home-page/{slug}/{status}", name="admin.api_product_change_home_page", methods={"PATCH"}, options={"expose": true})
     * @param ProductTranslation $productTranslation
     * @param int                $status
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public function toggleHomePage(ProductTranslation $productTranslation, int $status): JsonResponse
    {
        $this->editHandler->toggleHomePage($productTranslation->getProduct(), (bool) $status);

        return $this->json(null);
    }
}