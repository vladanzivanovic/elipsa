<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\CategoryTranslation;
use App\Entity\ProductColor;
use App\Entity\ProductSize;
use App\Entity\ProductTranslation;
use App\Handler\CategoryHandler;
use App\Handler\ProductColorHandler;
use App\Handler\ProductEditHandler;
use App\Handler\SizeHandler;
use App\Repository\OrderProductRepository;
use App\Repository\ProductHasColorRepository;
use App\Repository\ProductRepository;
use App\Repository\ShopOrderRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class ProductRemoveController extends AbstractController
{
    /**
     * @var CategoryHandler
     */
    private $categoryHandler;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var ProductEditHandler
     */
    private $handler;
    /**
     * @var ShopOrderRepository
     */
    private $orderRepository;
    /**
     * @var OrderProductRepository
     */
    private $orderProductRepository;

    /**
     * ProductRemoveController constructor.
     *
     * @param CategoryHandler        $categoryHandler
     * @param ProductRepository      $productRepository
     * @param ProductEditHandler     $handler
     * @param OrderProductRepository $orderProductRepository
     */
    public function __construct(
        CategoryHandler $categoryHandler,
        ProductRepository $productRepository,
        ProductEditHandler $handler,
        OrderProductRepository $orderProductRepository
    ) {
        $this->categoryHandler = $categoryHandler;
        $this->productRepository = $productRepository;
        $this->handler = $handler;
        $this->orderProductRepository = $orderProductRepository;
    }

    /**
     * @Route("/api/remove-product/{slug}", name="admin.remove_product_api", methods={"DELETE"}, options={"expose": true})
     *
     * @param ProductTranslation $productTranslation
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(ProductTranslation $productTranslation)
    {
        $product = $productTranslation->getProduct();
        $productCount = $this->orderProductRepository->count(['product' => $product]);

        if ($productCount > 0) {
            return $this->json(['message' => 'error.in_use'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->handler->remove($product);

        return $this->json([]);
    }
}