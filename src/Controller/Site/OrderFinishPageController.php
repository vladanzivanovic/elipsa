<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Exception\OrderException;
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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OrderFinishPageController extends AbstractController
{
    private OrderHandler $handler;

    private OrderFinishPageFormatter $pageFormatter;

    private OrderCompleteMailer $orderCompleteMailer;

    private OrderFinishParser $orderFinishParser;

    private OrderEditResponseFormatter $responseFormatter;

    private TranslatorInterface $translator;

    public function __construct(
        OrderHandler $handler,
        OrderFinishPageFormatter $pageFormatter,
        OrderCompleteMailer $orderCompleteMailer,
        OrderFinishParser $orderFinishParser,
        OrderEditResponseFormatter $responseFormatter,
        TranslatorInterface $translator
    ) {
        $this->handler = $handler;
        $this->pageFormatter = $pageFormatter;
        $this->orderCompleteMailer = $orderCompleteMailer;
        $this->orderFinishParser = $orderFinishParser;
        $this->responseFormatter = $responseFormatter;
        $this->translator = $translator;
    }

    /**
     * @Route({
     *          "rs": "/korpa/uspesna-narudzbina/{token}",
     *          "en": "/cart/success-order/{token}"
     *     },
     *     name="site.checkout_completed_successful",
     *     methods={"POST", "GET"},
     *     options={"expose": true}
     * )
     * @Template("Site/Pages/checkoutFinish.html.twig")
     *
     * @param Request $request
     * @return array|RedirectResponse
     *
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws \ReflectionException
     */
    public function successPage(Request $request, string $token)
    {
        $locale = $request->attributes->get('_locale');

        $isSuccessfulTransaction = true;

        try {
            $order = $this->orderFinishParser->parse($token, $isSuccessfulTransaction, $request->getMethod() === Request::METHOD_POST ? $request->request : null);
        } catch (OrderException $orderException) {
            $request->getSession()->getFlashBag()->add(
                'message',
                $this->translator->trans(
                    $orderException->getMessage(),
                    $orderException->getParameters(),
                    $orderException->getDomain(),
                    $locale
                )
            );

            return $this->redirectToRoute('site.home_page');
        }

        $this->handler->save($order, 'CompleteOrderOnSuccess');

        $viewData = $this->pageFormatter->formatResponse(
            $order,
            $locale,
            $isSuccessfulTransaction
        );

        $this->orderCompleteMailer->sendEmail($viewData, $order, $locale);

        return $viewData;
    }

    /**
     * @Route({
     *          "rs": "/korpa/neuspesna-narudzbina/{token}",
     *          "en": "/cart/unsuccessful-order{token}"
     *     },
     *     name="site.checkout_failed",
     *     methods={"POST", "GET"},
     *     options={"expose": true}
     * )
     * @Template("Site/Pages/checkoutFinish.html.twig")
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     * @throws \ReflectionException
     */
    public function unsuccessfulPage(Request $request, string $token)
    {
        $locale = $request->attributes->get('_locale');

        $isSuccessfulTransaction = false;

        try {
            $order = $this->orderFinishParser->parse($token, $isSuccessfulTransaction, $request->getMethod() === Request::METHOD_POST ? $request->request : null);
        } catch (OrderException $orderException) {
            $request->getSession()->getFlashBag()->add(
                'message',
                $this->translator->trans(
                    $orderException->getMessage(),
                    $orderException->getParameters(),
                    $orderException->getDomain(),
                    $locale
                )
            );

            return $this->redirectToRoute('site.home_page');
        }

        $this->handler->save($order, 'FailedOrder');

        $viewData = $this->pageFormatter->formatResponse(
            $order,
            $locale,
            $isSuccessfulTransaction
        );

        $this->orderCompleteMailer->sendEmail($viewData, $order, $locale);

        return $viewData;
    }
}
