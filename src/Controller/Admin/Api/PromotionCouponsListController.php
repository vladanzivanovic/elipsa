<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Promotion;
use App\Entity\Tags;
use App\Formatter\Admin\BannerDataTableResponseFormatter;
use App\Formatter\Admin\ProductColorDataTableResponseFormatter;
use App\Formatter\Admin\ProductDataTableResponseFormatter;
use App\Formatter\Admin\ProductTagDataTableResponseFormatter;
use App\Formatter\Admin\PromotionCouponsDataTableResponseFormatter;
use App\Formatter\Admin\SliderDataTableResponseFormatter;
use App\Parser\DataTableRequestParser;
use App\Repository\BannerRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\TagsRepository;
use App\Repository\PromotionRepository;
use App\Repository\SliderRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class PromotionCouponsListController extends AbstractController
{
    private DataTableRequestParser $requestParser;

    private PromotionCouponsDataTableResponseFormatter $responseFormatter;

    private PromotionRepository $couponsRepository;

    public function __construct(
        DataTableRequestParser $requestParser,
        PromotionRepository $couponsRepository,
        PromotionCouponsDataTableResponseFormatter $responseFormatter
    ) {
        $this->requestParser = $requestParser;
        $this->responseFormatter = $responseFormatter;
        $this->couponsRepository = $couponsRepository;
    }

    /**
     * @Route("/api/promotion/coupons/list", name="admin.get_promotion_coupons_list", methods={"POST"}, options={"expose": true})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getCouponsList(Request $request): JsonResponse
    {
        $formattedRequest = $this->requestParser->formatRequest($request);
        $total = $this->couponsRepository->countData();

        $data = $this->couponsRepository->getAdminList($formattedRequest, Promotion::TYPE_COUPON);

        $response = $this->responseFormatter->formatResponse($formattedRequest, $data, (int)$total);

        return new JsonResponse($response);
    }

    /**
     * @Route("/api/promotion/products/list", name="admin.get_promotion_products_list", methods={"POST"}, options={"expose": true})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getProductsList(Request $request): JsonResponse
    {
        $formattedRequest = $this->requestParser->formatRequest($request);
        $total = $this->couponsRepository->countData();

        $data = $this->couponsRepository->getAdminList($formattedRequest, Promotion::TYPE_PRODUCT);

        $response = $this->responseFormatter->formatResponse($formattedRequest, $data, (int)$total);

        return new JsonResponse($response);
    }
}
