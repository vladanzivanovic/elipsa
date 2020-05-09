<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Collector\CartPageCollector;
use App\Formatter\Site\CartPageFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CartPageController extends AbstractController
{
    /**
     * @var CartPageCollector
     */
    private $pageCollector;
    /**
     * @var CartPageFormatter
     */
    private $pageFormatter;

    /**
     * CartPageController constructor.
     *
     * @param CartPageCollector $pageCollector
     * @param CartPageFormatter $pageFormatter
     */
    public function __construct(
        CartPageCollector $pageCollector,
        CartPageFormatter $pageFormatter
    ) {
        $this->pageCollector = $pageCollector;
        $this->pageFormatter = $pageFormatter;
    }

    /**
     * @Route("/korpa", name="site.cart_page", methods={"GET"})
     * @Template("Site/Pages/cart.html.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function index(Request $request): array
    {
        if (false === $request->getSession()->has('order')) {
            return ['total' => 0, 'shipping' => 0, 'promo_price' => 0, 'free_shipping_price' => 0, 'shipping_price' => 0];
        }

        $orderData = $this->pageCollector->collect($request->getLocale());

        return $this->pageFormatter->formatResponse($orderData);
    }

    /**
     * @Route("/korpa/unos-podataka", name="site.checkout_page", methods={"GET"})
     * @Template("Site/Pages/checkout.html.twig")
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function checkout(Request $request)
    {
        if (false === $request->getSession()->has('order')) {
            return $this->redirectToRoute('site.home_page');
        }

        $products = $this->pageCollector->collect($request->getLocale());

        return $this->pageFormatter->formatResponse($products);
    }
}