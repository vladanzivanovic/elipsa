<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Tags;
use App\Handler\TagHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TagRemoveController extends AbstractController
{
    /**
     * @var TagHandler
     */
    private $tagHandler;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TagHandler          $tagHandler
     * @param TranslatorInterface $translator
     */
    public function __construct(
        TagHandler $tagHandler,
        TranslatorInterface $translator
    ) {
        $this->tagHandler = $tagHandler;
        $this->translator = $translator;
    }

    /**
     * @Route("/api/remove-product-tag/{slug}", name="admin.remove_product_tag_api", methods={"DELETE"}, options={"expose": true})
     *
     * @param Tags $productTags
     *
     * @return JsonResponse
     */
    public function removeProductTag(Tags $productTags)
    {
        $mainSlug = $productTags->getMainSlug();

        $this->tagHandler->removeFromProducts($mainSlug);

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

        $this->tagHandler->removeFromBlog($mainSlug);

        return $this->json([]);
    }
}