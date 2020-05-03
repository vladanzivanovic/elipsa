<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Entity\Product;
use App\Entity\ProductTranslation;
use App\Formatter\Site\OrderEditResponseFormatter;
use App\Handler\Site\OrderHandler;
use App\Parser\Site\OrderEditRequestParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class OrderEditController extends AbstractController
{
    /**
     * @var OrderEditRequestParser
     */
    private $requestParser;
    /**
     * @var OrderHandler
     */
    private $orderHandler;
    /**
     * @var OrderEditResponseFormatter
     */
    private $responseFormatter;

    /**
     * OrderEditController constructor.
     *
     * @param OrderEditRequestParser     $requestParser
     * @param OrderHandler               $orderHandler
     * @param OrderEditResponseFormatter $responseFormatter
     */
    public function __construct(
        OrderEditRequestParser $requestParser,
        OrderHandler $orderHandler,
        OrderEditResponseFormatter $responseFormatter
    ) {
        $this->requestParser = $requestParser;
        $this->orderHandler = $orderHandler;
        $this->responseFormatter = $responseFormatter;
    }

    /**
     * @Route("/api/order/set/{slug}", name="site_api.set_order", methods={"POST"}, options={"expose": true})
     *
     * @param Request            $request
     * @param ProductTranslation $productTranslation
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Request $request, ProductTranslation $productTranslation): JsonResponse
    {
        $order = $this->requestParser->parse($request, $productTranslation->getProduct());

        $this->orderHandler->save($order);

        $request->getSession()->set('order', $order->getId());

        return $this->json($this->responseFormatter->formatResponse(
            $productTranslation,
            $order,
            $request->request->get('size'),
            $request->request->getInt('color'),
            $request->request->getInt('quantity')
        ), JsonResponse::HTTP_CREATED);
    }
}