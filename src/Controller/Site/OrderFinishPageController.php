<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Formatter\Site\OrderFinishPageFormatter;
use App\Handler\Site\OrderHandler;
use App\Repository\ShopOrderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class OrderFinishPageController extends AbstractController
{
    /**
     * @var OrderHandler
     */
    private $handler;

    /**
     * @var OrderFinishPageFormatter
     */
    private $pageFormatter;

    /**
     * @param OrderHandler             $handler
     * @param OrderFinishPageFormatter $pageFormatter
     */
    public function __construct(
        OrderHandler $handler,
        OrderFinishPageFormatter $pageFormatter
    ) {
        $this->handler = $handler;
        $this->pageFormatter = $pageFormatter;
    }

    /**
     * @Route({
     *          "rs": "/korpa/uspesna-narudzbina",
     *          "en": "/cart/success-order"
     *     },
     *     name="site.checkout_completed_successful",
     *     methods={"POST", "GET"},
     *     options={"expose": true}
     * )
     * @Template("Site/Pages/checkoutFinish.html.twig")
     *
     * @param Request $request
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    public function successPage(Request $request): array
    {
        $locale = $request->getSession()->get('_locale');
        $orderId = $request->getMethod() === Request::METHOD_POST ?
            $request->request->getInt('ReturnOid') : $request->getSession()->get('order');

        $data = $this->handler->completeCheckoutOnSuccess($orderId, $locale, $request->request);
        $request->getSession()->remove('order');

        return $this->pageFormatter->formatResponse($data, $locale, $request->request);
    }

    /**
     * @Route({
     *          "rs": "/korpa/neuspesna-narudzbina",
     *          "en": "/cart/unsuccessful-order"
     *     },
     *     name="site.checkout_failed",
     *     methods={"POST", "GET"},
     *     options={"expose": true}
     * )
     * @Template("Site/Pages/checkoutFinish.html.twig")
     *
     * @param Request $request
     *
     * @return array
     * @throws \ReflectionException
     */
    public function unsuccessfulPage(Request  $request): array
    {
        $locale = $request->getSession()->get('_locale');
        $orderId = $request->getMethod() === Request::METHOD_POST ?
            $request->request->getInt('oid') : $request->getSession()->get('order');

        $data = $this->handler->completeCheckoutOnFail($orderId, $request->request);

        $request->getSession()->set('order', $orderId);

        return $this->pageFormatter->formatResponse($data, $locale, $request->request);
    }
}