<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Banner;
use App\Entity\ProductColor;
use App\Entity\Tags;
use App\Entity\Promotion;
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
use Symfony\Component\Routing\Attribute\Route;

final class PromotionRemoveController extends AbstractController
{
    private CouponHandler $couponHandler;

    public function __construct(
        CouponHandler $couponHandler
    ) {
        $this->couponHandler = $couponHandler;
    }

    /**
     *
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    #[Route(path: '/api/promotion/remove/{id}', name: 'admin.remove_promotion_api', methods: ['DELETE'], options: ['expose' => true])]
    public function remove(Promotion $coupon): JsonResponse
    {
        $this->couponHandler->remove($coupon);

        return $this->json(null);
    }
}
