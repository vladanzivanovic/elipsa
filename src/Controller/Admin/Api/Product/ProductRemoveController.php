<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api\Product;

use App\Entity\Product;
use App\Entity\ProductTranslation;
use App\Handler\CategoryHandler;
use App\Handler\ProductEditHandler;
use App\Repository\OrderProductRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProductRemoveController extends AbstractController
{
    private ProductEditHandler $handler;

    private OrderProductRepository $orderProductRepository;

    private TranslatorInterface $translator;

    public function __construct(
        CategoryHandler $categoryHandler,
        ProductRepository $productRepository,
        ProductEditHandler $handler,
        OrderProductRepository $orderProductRepository,
        TranslatorInterface $translator
    ) {
        $this->handler = $handler;
        $this->orderProductRepository = $orderProductRepository;
        $this->translator = $translator;
    }

    
    #[Route(path: '/api/remove-product/{slug}', name: 'admin.remove_product_api', methods: ['DELETE'], options: ['expose' => true])]
    public function remove(ProductTranslation $productTranslation): JsonResponse
    {
        $product = $productTranslation->getProduct();
        $productCount = $this->orderProductRepository->count(['product' => $product]);

        if ($productCount > 0) {
            $product->setStatus(Product::STATUS_ARCHIVED);

            $this->handler->save($product);

            return $this->json(['message' => $this->translator->trans('product.is_archived', ['%item%' => $productTranslation->getTitle()])], Response::HTTP_OK);
        }

        $this->handler->remove($product);

        return $this->json(['message' => $this->translator->trans('product.is_deleted', ['%item%' => $productTranslation->getTitle()])], Response::HTTP_OK);
    }
}
