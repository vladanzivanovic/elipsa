<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api\Order;

use App\Formatter\Admin\OrderDataTableResponseFormatter;
use App\Parser\DataTableRequestParser;
use App\Repository\ShopOrderRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class OrdersListController extends AbstractController
{
    private DataTableRequestParser $requestParser;

    private OrderDataTableResponseFormatter $responseFormatter;

    private ShopOrderRepository $repository;

    public function __construct(
        DataTableRequestParser $requestParser,
        OrderDataTableResponseFormatter $responseFormatter,
        ShopOrderRepository $repository
    ) {
        $this->requestParser = $requestParser;
        $this->responseFormatter = $responseFormatter;
        $this->repository = $repository;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route(path: '/api/get-order-list', name: 'admin.get_order_list', options: ['expose' => true], methods: ['POST'])]
    public function getList(Request $request): JsonResponse
    {
        $formattedRequest = $this->requestParser->formatRequest($request);
        $total = $this->repository->countData();

        $data = $this->repository->getAdminList($formattedRequest);

        $response = $this->responseFormatter->formatResponse($formattedRequest, $data, (int)$total);

        return new JsonResponse($response);
    }
}
