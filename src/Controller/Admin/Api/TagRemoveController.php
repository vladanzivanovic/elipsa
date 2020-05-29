<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\ProductColor;
use App\Entity\Tags;
use App\Handler\ProductColorHandler;
use App\Handler\TagHandler;
use App\Repository\BlogHasTagsRepository;
use App\Repository\ProductHasColorRepository;
use App\Repository\ProductHasTagsRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class TagRemoveController extends AbstractController
{
    /**
     * @var TagHandler
     */
    private $tagHandler;
    /**
     * @var ProductHasTagsRepository
     */
    private $hasTagsRepository;
    /**
     * @var BlogHasTagsRepository
     */
    private $blogHasTagsRepository;

    /**
     * @param TagHandler               $tagHandler
     * @param ProductHasTagsRepository $hasTagsRepository
     * @param BlogHasTagsRepository    $blogHasTagsRepository
     */
    public function __construct(
        TagHandler $tagHandler,
        ProductHasTagsRepository $hasTagsRepository,
        BlogHasTagsRepository $blogHasTagsRepository
    ) {
        $this->tagHandler = $tagHandler;
        $this->hasTagsRepository = $hasTagsRepository;
        $this->blogHasTagsRepository = $blogHasTagsRepository;
    }

    /**
     * @Route("/api/remove--product-tag/{slug}", name="admin.remove_product_tag_api", methods={"DELETE"}, options={"expose": true})
     *
     * @param Tags $productTags
     *
     * @return JsonResponse
     */
    public function removeProductTag(Tags $productTags)
    {
        $mainSlug = $productTags->getMainSlug();

        $productCount = $this->hasTagsRepository->count(['tag' => $mainSlug]);

        if ($productCount > 0) {
            throw new BadRequestHttpException(json_encode(['message' => 'error.in_use']));
        }

        $this->tagHandler->remove($mainSlug);

        return $this->json([]);
    }

    /**
     * @Route("/api/remove-blog-tag/{slug}", name="admin.remove_blog_tag_api", methods={"DELETE"}, options={"expose": true})
     *
     * @param Tags $tags
     *
     * @return JsonResponse
     */
    public function removeBlogTag(Tags $tags)
    {
        $mainSlug = $tags->getMainSlug();

        $blogCount = $this->blogHasTagsRepository->count(['tag' => $mainSlug]);

        if ($blogCount > 0) {
            throw new BadRequestHttpException(json_encode(['message' => 'error.in_use']));
        }

        $this->tagHandler->remove($mainSlug);

        return $this->json([]);
    }
}