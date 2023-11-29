<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Banner;
use App\Entity\PromotionCoupon;
use App\Formatter\Admin\BannerEditResponseFormatter;
use App\Formatter\Admin\CouponEditResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;

final class PromotionCouponsEditPageController extends AbstractController
{
    private CouponEditResponseFormatter $responseFormatter;

    public function __construct(
        CouponEditResponseFormatter $responseFormatter
    ) {
        $this->responseFormatter = $responseFormatter;
    }

    /**
     * @Route("/add-coupon", name="admin.add_coupon_page", methods={"GET"})
     * @Template("Admin/Pages/couponsEdit.html.twig")
     *
     * @return array
     */
    public function insert(): array
    {
        return $this->responseFormatter->formatResponse();
    }

    /**
     * @Route("/edit-coupon/{id}", name="admin.edit_coupon_page", methods={"GET"}, options={"exponse": true})
     * @Template("Admin/Pages/couponsEdit.html.twig")
     *
     * @param PromotionCoupon $coupon
     *
     * @return array
     */
    public function update(PromotionCoupon $coupon): array
    {
//        dd($this->responseFormatter->formatResponse($coupon));
        return $this->responseFormatter->formatResponse($coupon);
    }
}
