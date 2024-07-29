<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Formatter\Admin\ProductColorDataTableResponseFormatter;
use App\Formatter\Admin\ProductDataTableResponseFormatter;
use App\Parser\DataTableRequestParser;
use App\Repository\ProductColorRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ProductColorsListController extends AbstractController
{
    private \App\Parser\DataTableRequestParser $requestParser;

    /**
     * @var ProductDataTableResponseFormatter
     */
    private \App\Formatter\Admin\ProductColorDataTableResponseFormatter $responseFormatter;

    private \App\Repository\ProductColorRepository $colorRepository;

    /**
     * ProductColorsListController constructor.
     */
    public function __construct(
        DataTableRequestParser $requestParser,
        ProductColorRepository $colorRepository,
        ProductColorDataTableResponseFormatter $responseFormatter
    ) {
        $this->requestParser = $requestParser;
        $this->responseFormatter = $responseFormatter;
        $this->colorRepository = $colorRepository;
    }

    /**
     *
     *
     * @return JsonResponse
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route(path: '/api/get-product-colors-list', name: 'admin.get_product_colors_list', options: ['expose' => true], methods: ['POST'])]
    public function getList(Request $request): JsonResponse
    {
        $formattedRequest = $this->requestParser->formatRequest($request);
        $total = $this->colorRepository->countData();

        $data = $this->colorRepository->getAdminList($formattedRequest);

        $response = $this->responseFormatter->formatResponse($formattedRequest, $data, (int)$total);

        return new JsonResponse($response);
    }
}
