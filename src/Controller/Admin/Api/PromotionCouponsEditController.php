<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Banner;
use App\Entity\Promotion;
use App\Handler\CouponHandler;
use App\Helper\ConstantsHelper;
use App\Parser\CouponsEditRequestParser;
use App\Request\Dto\PromotionCouponRequestDto;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PromotionCouponsEditController extends AbstractController
{
    private CouponsEditRequestParser $requestParser;

    private CouponHandler $couponHandler;

    private TranslatorInterface $translator;

    public function __construct(
        CouponsEditRequestParser $requestParser,
        CouponHandler $couponHandler,
        TranslatorInterface $translator
    ) {
        $this->requestParser = $requestParser;
        $this->couponHandler = $couponHandler;
        $this->translator = $translator;
    }

    /**
     * @Route("/api/promotion/add", name="admin.add_promotion_api", methods={"POST"})
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
     * @Route("/api/promotion/{id}", name="admin.edit_promotion_api", methods={"PUT"})
     *
     * @param PromotionCouponRequestDto $promotionCouponRequestDto
     * @param Promotion $coupon
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(PromotionCouponRequestDto $promotionCouponRequestDto, Promotion $coupon): JsonResponse
    {
        $coupon = $this->requestParser->parse($promotionCouponRequestDto, $coupon);

        $this->couponHandler->save($coupon);

        return $this->json(null, Response::HTTP_CREATED);
    }
}
