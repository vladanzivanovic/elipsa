<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Tags;
use App\Formatter\Admin\BannerDataTableResponseFormatter;
use App\Formatter\Admin\LocationDataTableResponseFormatter;
use App\Formatter\Admin\LoyaltyDataTableResponseFormatter;
use App\Formatter\Admin\OrderDataTableResponseFormatter;
use App\Formatter\Admin\ProductColorDataTableResponseFormatter;
use App\Formatter\Admin\ProductDataTableResponseFormatter;
use App\Formatter\Admin\ProductTagDataTableResponseFormatter;
use App\Formatter\Admin\SliderDataTableResponseFormatter;
use App\Parser\DataTableRequestParser;
use App\Repository\BannerRepository;
use App\Repository\LocationRepository;
use App\Repository\LoyaltyRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\ShopOrderRepository;
use App\Repository\TagsRepository;
use App\Repository\SliderRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class OrdersListController extends AbstractController
{
    /**
     * @var DataTableRequestParser
     */
    private $requestParser;

    /**
     * @var OrderDataTableResponseFormatter
     */
    private $responseFormatter;
    /**
     * @var ShopOrderRepository
     */
    private $repository;

    /**
     * @param DataTableRequestParser          $requestParser
     * @param OrderDataTableResponseFormatter $responseFormatter
     * @param ShopOrderRepository             $repository
     */
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
     * @Route("/api/get-order-list", name="admin.get_order_list", methods={"POST"}, options={"expose": true})
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
        $total = $this->repository->countData();

        $data = $this->repository->getAdminList($formattedRequest);

        $response = $this->responseFormatter->formatResponse($formattedRequest, $data, (int)$total);

        return new JsonResponse($response);
    }
}