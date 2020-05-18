<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Banner;
use App\Entity\PromotionCoupon;
use App\Handler\CouponHandler;
use App\Helper\ConstantsHelper;
use App\Parser\CouponsEditRequestParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CouponsEditController extends AbstractController
{
    /**
     * @var CouponsEditRequestParser
     */
    private $requestParser;

    /**
     * @var CouponHandler
     */
    private $couponHandler;

    /**
     * @param CouponsEditRequestParser $requestParser
     * @param CouponHandler            $couponHandler
     */
    public function __construct(
        CouponsEditRequestParser $requestParser,
        CouponHandler $couponHandler
    ) {
        $this->requestParser = $requestParser;
        $this->couponHandler = $couponHandler;
    }

    /**
     * @Route("/api/add-coupon", name="admin.add_coupon_api", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function insert(Request $request): JsonResponse
    {
        $coupon = $this->requestParser->parse($request->request);

        $this->couponHandler->save($coupon);

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/edit-coupon/{id}", name="admin.edit_coupon_api", methods={"PUT"}, options={"expose": true})
     *
     * @param Request         $request
     * @param PromotionCoupon $coupon
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(Request $request, PromotionCoupon $coupon): JsonResponse
    {
        $coupon = $this->requestParser->parse($request->request, $coupon);

        $this->couponHandler->save($coupon);

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }
}