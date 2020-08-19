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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CatalogRemoveController extends AbstractController
{
    /**
     * @var CategoryHandler
     */
    private $categoryHandler;

    /**
     * @var CatalogHandler
     */
    private $handler;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param CategoryHandler     $categoryHandler
     * @param CatalogHandler      $handler
     * @param TranslatorInterface $translator
     */
    public function __construct(
        CategoryHandler $categoryHandler,
        CatalogHandler $handler,
        TranslatorInterface $translator
    ) {
        $this->categoryHandler = $categoryHandler;
        $this->handler = $handler;
        $this->translator = $translator;
    }

    /**
     * @Route("/api/remove-catalog/{id}", name="admin.remove_catalog_api", methods={"DELETE"}, options={"expose": true})
     *
     * @param Catalogue $catalogue
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(Catalogue $catalogue)
    {
        $this->handler->remove($catalogue);

        return $this->json([]);
    }
}