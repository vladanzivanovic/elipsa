<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Banner;
use App\Entity\Promotion;
use App\Formatter\Admin\BannerEditResponseFormatter;
use App\Formatter\Admin\CouponEditResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;

final class PromotionEditPageController extends AbstractController
{
    private CouponEditResponseFormatter $responseFormatter;

    public function __construct(
        CouponEditResponseFormatter $responseFormatter
    ) {
        $this->responseFormatter = $responseFormatter;
    }

    /**
     * @Route("/promotion/coupon/add", name="admin.add_promotion_coupon_page", methods={"GET"})
     * @Route("/promotion/products/add", name="admin.add_promotion_product_page", methods={"GET"})
     * @Template("Admin/Pages/promotionEdit.html.twig")
     *
     * @return array
     */
    public function insert(): array
    {
        return $this->responseFormatter->formatResponse();
    }

    /**
     * @Route("/promotion/coupon/{id}", name="admin.edit_promotion_coupon_page", methods={"GET"})
     * @Route("/promotion/products/{id}", name="admin.edit_promotion_product_page", methods={"GET"})
     * @Template("Admin/Pages/promotionEdit.html.twig")
     *
     * @param Promotion $coupon
     *
     * @return array
     */
    public function update(Promotion $coupon): array
    {
        return $this->responseFormatter->formatResponse($coupon);
    }
}
