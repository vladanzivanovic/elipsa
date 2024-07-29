<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Promotion;
use App\Handler\CouponHandler;
use App\Parser\CouponsEditRequestParser;
use App\Request\Dto\Admin\PromotionCouponRequestDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
     * @throws \Exception
     */
    #[Route(path: '/api/promotion/add', name: 'admin.add_promotion_api', methods: ['POST'])]
    public function insert(PromotionCouponRequestDto $promotionCouponRequestDto): JsonResponse
    {
        $coupon = $this->requestParser->parse($promotionCouponRequestDto);

        $this->couponHandler->save($coupon);

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     *
     * @throws \Exception
     */
    #[Route(path: '/api/promotion/{id}', name: 'admin.edit_promotion_api', methods: ['PUT'])]
    public function update(PromotionCouponRequestDto $promotionCouponRequestDto, Promotion $coupon): JsonResponse
    {
        $coupon = $this->requestParser->parse($promotionCouponRequestDto, $coupon);

        $this->couponHandler->save($coupon);

        return $this->json(null, Response::HTTP_CREATED);
    }
}
