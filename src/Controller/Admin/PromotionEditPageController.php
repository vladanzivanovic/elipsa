<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Banner;
use App\Entity\Promotion;
use App\Formatter\Admin\BannerEditResponseFormatter;
use App\Formatter\Admin\CouponEditResponseFormatter;
use Symfony\Bridge\Twig\Attribute\Template;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Attribute\Route;

final class PromotionEditPageController extends AbstractController
{
    private CouponEditResponseFormatter $responseFormatter;

    public function __construct(
        CouponEditResponseFormatter $responseFormatter
    ) {
        $this->responseFormatter = $responseFormatter;
    }

    #[Route(path: '/promotions/add', name: 'admin.add_promotions_page', methods: ['GET'])]
    #[Template('Admin/Pages/promotionEdit.html.twig')]
    public function insert(): array
    {
        return $this->responseFormatter->formatResponse();
    }

    
    #[Route(path: '/promotions/{id}', name: 'admin.edit_promotions_page', methods: ['GET'])]
    #[Template('Admin/Pages/promotionEdit.html.twig')]
    public function update(Promotion $coupon): array
    {
        return $this->responseFormatter->formatResponse($coupon);
    }
}
