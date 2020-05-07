<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Handler\Site\OrderHandler;
use App\Parser\Site\OrderCompleteRequestParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class OrderCompleteController extends AbstractController
{
    /**
     * @var OrderCompleteRequestParser
     */
    private $requestParser;
    /**
     * @var OrderHandler
     */
    private $orderHandler;

    /**
     * @param OrderCompleteRequestParser $requestParser
     * @param OrderHandler               $orderHandler
     */
    public function __construct(
        OrderCompleteRequestParser $requestParser,
        OrderHandler $orderHandler
    ) {
        $this->requestParser = $requestParser;
        $this->orderHandler = $orderHandler;
    }

    /**
     * @Route("/api/order/complete", name="site_api.complete_order", methods={"PUT"}, options={"expose": true})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function completeOrder(Request $request): JsonResponse
    {
        $csrf = $request->request->get('_csrf_token');

        if (false === $this->isCsrfTokenValid('order_complete', $csrf)) {
            $this->createAccessDeniedException();
        }

        $order = $this->requestParser->parse($request->request, $request->getSession()->get('order'));

        $this->orderHandler->save($order, true);

        $request->getSession()->remove('order');

        return $this->json(null);
    }
}