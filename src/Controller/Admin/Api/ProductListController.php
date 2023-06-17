<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Formatter\Admin\ProductDataTableResponseFormatter;
use App\Parser\DataTableRequestParser;
use App\Repository\ProductRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class ProductListController extends AbstractController
{
    private DataTableRequestParser $requestParser;

    private ProductRepository $productRepository;

    private ProductDataTableResponseFormatter $responseFormatter;

    public function __construct(
        DataTableRequestParser $requestParser,
        ProductRepository $productRepository,
        ProductDataTableResponseFormatter $responseFormatter
    ) {
        $this->requestParser = $requestParser;
        $this->productRepository = $productRepository;
        $this->responseFormatter = $responseFormatter;
    }

    /**
     * @Route("/api/get-product-list", name="admin.get_product_list", methods={"POST"}, options={"expose": true})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getList(Request $request)
    {
        $formattedRequest = $this->requestParser->formatRequest($request);
        $total = $this->productRepository->countData($formattedRequest);

        $data = $this->productRepository->getAdminList($formattedRequest);

        $response = $this->responseFormatter->formatResponse($formattedRequest, $data, (int)$total);

        return new JsonResponse($response);
    }
}
