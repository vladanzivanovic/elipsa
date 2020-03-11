<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\ProductColor;
use App\Entity\ProductTags;
use App\Handler\ProductColorHandler;
use App\Handler\ProductTagHandler;
use App\Repository\ProductHasColorRepository;
use App\Repository\ProductHasTagsRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class ProductTagRemoveController extends AbstractController
{
    /**
     * @var ProductTagHandler
     */
    private $tagHandler;
    /**
     * @var ProductHasTagsRepository
     */
    private $hasTagsRepository;

    /**
     * ProductTagRemoveController constructor.
     *
     * @param ProductTagHandler        $tagHandler
     * @param ProductHasTagsRepository $hasTagsRepository
     */
    public function __construct(
        ProductTagHandler $tagHandler,
        ProductHasTagsRepository $hasTagsRepository
    ) {
        $this->tagHandler = $tagHandler;
        $this->hasTagsRepository = $hasTagsRepository;
    }

    /**
     * @Route("/api/remove-tag/{slug}", name="admin.remove_tag_api", methods={"DELETE"}, options={"expose": true})
     *
     * @param ProductTags $productTags
     *
     * @return JsonResponse
     */
    public function remove(ProductTags $productTags)
    {
        $mainSlug = $productTags->getMainSlug();

        $productCount = $this->hasTagsRepository->count(['tag' => $mainSlug]);

        if ($productCount > 0) {
            throw new BadRequestHttpException(json_encode(['message' => 'error.in_use']));
        }

        $this->tagHandler->remove($mainSlug);

        return $this->json([]);
    }
}