<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Banner;
use App\Entity\ProductColor;
use App\Entity\Tags;
use App\Entity\PromotionCoupon;
use App\Entity\Slider;
use App\Handler\BannerHandler;
use App\Handler\CouponHandler;
use App\Handler\ProductColorHandler;
use App\Handler\TagHandler;
use App\Handler\SliderHandler;
use App\Repository\ProductHasColorRepository;
use App\Repository\ProductHasTagsRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class CouponRemoveController extends AbstractController
{
    /**
     * @var CouponHandler
     */
    private $couponHandler;

    /**
     * @param CouponHandler $couponHandler
     */
    public function __construct(
        CouponHandler $couponHandler
    ) {
        $this->couponHandler = $couponHandler;
    }

    /**
     * @Route("/api/remove-coupon/{id}", name="admin.remove_coupon_api", methods={"DELETE"}, options={"expose": true})
     *
     * @param PromotionCoupon $coupon
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(PromotionCoupon $coupon): JsonResponse
    {
        $this->couponHandler->remove($coupon);

        return $this->json(null);
    }
}