<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ShopOrder;
use App\Formatter\Admin\OrderSingleResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;

final class OrderSinglePageController extends AbstractController
{
    /**
     * @var OrderSingleResponseFormatter
     */
    private $responseFormatter;
    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * @param ParameterBagInterface        $bag
     * @param OrderSingleResponseFormatter $responseFormatter
     */
    public function __construct(
        ParameterBagInterface $bag,
        OrderSingleResponseFormatter $responseFormatter
    ) {
        $this->responseFormatter = $responseFormatter;
        $this->bag = $bag;
    }

    /**
     * @Route("/view-single-order/{id}", name="admin.view_single_order", methods={"GET"}, options={"expose": true})
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