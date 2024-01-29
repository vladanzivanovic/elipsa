<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Exception\OrderException;
use App\Formatter\Site\OrderEditResponseFormatter;
use App\Formatter\Site\OrderFinishPageFormatter;
use App\Handler\Site\OrderHandler;
use App\Mailer\OrderCompleteMailer;
use App\Parser\Site\Order\OrderFinishParser;
use App\Parser\Site\Order\OrderRequestParser;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OrderOverviewController extends AbstractController
{
    private OrderHandler $handler;

    private OrderFinishPageFormatter $pageFormatter;

    private OrderCompleteMailer $orderCompleteMailer;

    private OrderFinishParser $orderFinishParser;

    private OrderEditResponseFormatter $responseFormatter;

    private TranslatorInterface $translator;

    private OrderRequestParser $orderRequestParser;

    public function __construct(
        OrderHandler $handler,
        OrderFinishPageFormatter $pageFormatter,
        OrderCompleteMailer $orderCompleteMailer,
        OrderFinishParser $orderFinishParser,
        OrderEditResponseFormatter $responseFormatter,
        TranslatorInterface $translator,
        OrderRequestParser $orderRequestParser
    ) {
        $this->handler = $handler;
        $this->pageFormatter = $pageFormatter;
        $this->orderCompleteMailer = $orderCompleteMailer;
        $this->orderFinishParser = $orderFinishParser;
        $this->responseFormatter = $responseFormatter;
        $this->translator = $translator;
        $this->orderRequestParser = $orderRequestParser;
    }

    /**
     * @Route({
     *          "rs": "/korpa/pregled-porudzbine/{token}",
     *          "en": "/cart/order-overview/{token}",
     *          "ba": "/korpa/pregled-narudžbe/{token}"
     *     },
     *     name="site.order_overview",
     *     methods={"GET"},
     *     options={"expose": true}
     * )
     * @Template("Site/Pages/orderOverview.html.twig")
     *
     * @param Request $request
     * @return array|RedirectResponse
     *
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws \ReflectionException
     */
    public function orderOverview(Request $request, string $token)
    {
        $locale = $request->attributes->get('_locale');

        $isSuccessfulTransaction = true;

        try {
            $order = $this->orderRequestParser->findOrder($token);
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

        $viewData = $this->pageFormatter->formatResponse(
            $order,
            $locale,
            $isSuccessfulTransaction
        );
        
        return $viewData;
    }
}
