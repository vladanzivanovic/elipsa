<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Formatter\Site\OrderEditResponseFormatter;
use App\Formatter\Site\OrderFinishPageFormatter;
use App\Handler\Site\OrderHandler;
use App\Mailer\OrderCompleteMailer;
use App\Parser\Site\Order\OrderFinishParser;
use App\View\OrderFinishView;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class OrderFinishPageController extends AbstractController
{
    private OrderHandler $handler;

    private OrderFinishPageFormatter $pageFormatter;

    private OrderCompleteMailer $orderCompleteMailer;

    private OrderFinishParser $orderFinishParser;

    private OrderEditResponseFormatter $responseFormatter;

    public function __construct(
        OrderHandler $handler,
        OrderFinishPageFormatter $pageFormatter,
        OrderCompleteMailer $orderCompleteMailer,
        OrderFinishParser $orderFinishParser,
        OrderEditResponseFormatter $responseFormatter
    ) {
        $this->handler = $handler;
        $this->pageFormatter = $pageFormatter;
        $this->orderCompleteMailer = $orderCompleteMailer;
        $this->orderFinishParser = $orderFinishParser;
        $this->responseFormatter = $responseFormatter;
    }

    /**
     * @Route({
     *          "rs": "/korpa/uspesna-narudzbina/{token}",
     *          "en": "/cart/success-order/{token}"
     *     },
     *     name="site.checkout_completed_successful_basic",
     *     methods={"GET"},
     *     options={"expose": true}
     * )
     * @Template("Site/Pages/checkoutFinish.html.twig")
     *
     * @param Request $request
     * @return array
     *
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws \ReflectionException
     */
    public function successPage(Request $request, string $token): array
    {
        $locale = $request->attributes->get('_locale');

        $order = $this->orderFinishParser->parse($token);

        $this->handler->completeCheckoutOnSuccess($order, $locale, $request->request);

        $viewData = $this->pageFormatter->formatResponse(
            $order,
            $locale,
            true
        );

        $this->orderCompleteMailer->sendEmail($viewData, $order);

//        $request->getSession()->remove('order');

//        dd($viewData);

        return $viewData;
    }

    /**
     * @Route({
     *          "rs": "/korpa/uspesna-narudzbina/{token}",
     *          "en": "/cart/success-order/{token}"
     *     },
     *     name="site.checkout_completed_successful_card",
     *     methods={"POST"},
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
    public function successPageFromCardPayment(Request $request, string $token): array
    {

        $locale = $request->getSession()->get('_locale');
        $orderToken = $request->getMethod() === Request::METHOD_POST ?
            $request->request->get('oid') : $request->getSession()->get('order');

        $data = $this->handler->completeCheckoutOnSuccess($orderToken, $locale, $request->request);
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
        $orderToken = $request->getMethod() === Request::METHOD_POST ?
            $request->request->get('oid') : $request->getSession()->get('order');

        $data = $this->handler->completeCheckoutOnFail($orderToken, $locale, $request->request);

        $data['order']->setToken();

        $this->handler->save($data['order']);

        $request->getSession()->set('order', $data['order']->getToken());

        return $this->pageFormatter->formatResponse($data, $locale, $request->request);
    }
}
