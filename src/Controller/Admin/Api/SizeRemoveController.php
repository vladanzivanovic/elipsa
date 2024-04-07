<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\ProductSize;;
use App\Handler\SizeHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class SizeRemoveController extends AbstractController
{
    private \App\Handler\SizeHandler $handler;

    public function __construct(
        SizeHandler $handler
    ) {
        $this->handler = $handler;
    }

    /**
     *
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    #[Route(path: '/api/remove-size/{slug}', name: 'admin.remove_size_api', methods: ['DELETE'], options: ['expose' => true])]
    public function remove(ProductSize $size)
    {
        $productCount = $size->getProductHasSizes()->count();

        if ($productCount > 0) {
            return $this->json(['message' => 'error.in_use'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->handler->remove($size);

        return $this->json([]);
    }
}