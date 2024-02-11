<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ShopOrder;
use App\Formatter\Admin\OrderSingleResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class OrderSinglePageController extends AbstractController
{
    private OrderSingleResponseFormatter $responseFormatter;

    public function __construct(
        OrderSingleResponseFormatter $responseFormatter
    ) {
        $this->responseFormatter = $responseFormatter;
    }

    /**
     * @Route("/view-single-order/{token}", name="admin.view_single_order", methods={"GET"}, options={"expose": true})
     * @Template("Admin/Pages/invoice.html.twig")
     *
     * @param ShopOrder $order
     *
     * @return array
     */
    public function renderPage(ShopOrder $order): array
    {
        return $this->responseFormatter->formatResponse($order);
    }
}
