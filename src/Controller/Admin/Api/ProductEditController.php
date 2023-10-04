<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Product;
use App\Entity\ProductTranslation;
use App\Handler\ProductEditHandler;
use App\Helper\ConstantsHelper;
use App\Mailer\SizeAvailableMailer;
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

    private SizeAvailableMailer $sizeAvailableMailer;

    public function __construct(
        ProductEditRequestParser $requestParser,
        ProductEditHandler $editHandler,
        SizeAvailableMailer $sizeAvailableMailer
    ) {
        $this->requestParser = $requestParser;
        $this->editHandler = $editHandler;
        $this->sizeAvailableMailer = $sizeAvailableMailer;
    }

    /**
     * @Route("/api/add-product", name="admin.add_product_api", methods={"POST"}, options={"expose": true})
     * @throws ORMException
     */
    public function insert(Request $request): JsonResponse
    {
        $product = $this->requestParser->parse($request->request);

        $this->editHandler->save($product);

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/edit-product/{slug}", name="admin.edit_product_api", methods={"PUT"}, options={"expose": true})
     * @throws ORMException
     */
    public function update(Request $request, ProductTranslation $productTranslation): JsonResponse
    {
        $product = $this->requestParser->parse($request->request, $productTranslation->getProduct());

        $this->editHandler->save($product);

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/product-change-status/{slug}/{status}", name="admin.api_product_change_status", methods={"PATCH"}, options={"expose": true})
     */
    public function changeStatus(ProductTranslation $productTranslation, int $status): JsonResponse
    {
        $this->editHandler->changeStatus($productTranslation->getProduct(), $status);

        $statusText = ConstantsHelper::getConstantName((string) $status, 'STATUS', Product::class);

        return $this->json(['text' => $statusText]);
    }
}
