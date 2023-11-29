<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Banner;
use App\Entity\PromotionCoupon;
use App\Handler\CouponHandler;
use App\Helper\ConstantsHelper;
use App\Parser\CouponsEditRequestParser;
use App\Request\Dto\PromotionCouponRequestDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PromotionCouponsEditController extends AbstractController
{
    private CouponsEditRequestParser $requestParser;

    private CouponHandler $couponHandler;

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
     * @param PromotionCouponRequestDto $promotionCouponRequestDto
     * @return JsonResponse
     * @throws \Exception
     */
    public function insert(PromotionCouponRequestDto $promotionCouponRequestDto): JsonResponse
    {
        $coupon = $this->requestParser->parse($promotionCouponRequestDto);

        $this->couponHandler->save($coupon);

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/edit-coupon/{id}", name="admin.edit_coupon_api", methods={"PUT"}, options={"expose": true})
     *
     * @param PromotionCouponRequestDto $promotionCouponRequestDto
     * @param PromotionCoupon $coupon
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(PromotionCouponRequestDto $promotionCouponRequestDto, PromotionCoupon $coupon): JsonResponse
    {
        $coupon = $this->requestParser->parse($promotionCouponRequestDto, $coupon);

        $this->couponHandler->save($coupon);

        return $this->json(null, Response::HTTP_CREATED);
    }
}
