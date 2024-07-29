<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Catalogue;
use App\Entity\CategoryTranslation;
use App\Entity\ProductColor;
use App\Entity\ProductSize;
use App\Entity\ProductTranslation;
use App\Handler\CatalogHandler;
use App\Handler\CategoryHandler;
use App\Handler\ProductColorHandler;
use App\Handler\ProductEditHandler;
use App\Handler\SizeHandler;
use App\Repository\CatalogueRepository;
use App\Repository\OrderProductRepository;
use App\Repository\ProductHasColorRepository;
use App\Repository\ProductRepository;
use App\Repository\ShopOrderRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CatalogRemoveController extends AbstractController
{
    private \App\Handler\CatalogHandler $handler;

    public function __construct(
        CategoryHandler $categoryHandler,
        CatalogHandler $handler,
        TranslatorInterface $translator
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
    #[Route(path: '/api/remove-catalog/{id}', name: 'admin.remove_catalog_api', methods: ['DELETE'], options: ['expose' => true])]
    public function remove(Catalogue $catalogue)
    {
        $this->handler->remove($catalogue);

        return $this->json([]);
    }
}
