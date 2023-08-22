<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Tags;
use App\Entity\TagTranslation;
use App\Handler\TagHandler;
use App\Parser\TagRequestParser;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

final class TagRemoveController extends AbstractController
{
    private TagRequestParser $requestParser;

    private TagHandler $tagHandler;

    public function __construct(
        TagRequestParser $requestParser,
        TagHandler $tagHandler
    ) {
        $this->tagHandler = $tagHandler;
        $this->requestParser = $requestParser;
    }

    /**
     * @Route("/api/remove-product-tag/{slug}", name="admin.remove_product_tag_api", methods={"DELETE"}, options={"expose": true})
     *
     * @return JsonResponse
     * @throws ORMException
     */
    public function removeProductTag(string $slug): JsonResponse
    {
        $tag = $this->requestParser->getTagBySlug($slug);

        Assert::notNull($tag);

        $this->tagHandler->removeFromProducts($tag);

        return $this->json([]);
    }

    /**
     * @Route("/api/remove-blog-tag/{slug}", name="admin.remove_blog_tag_api", methods={"DELETE"}, options={"expose": true})
     *
     * @param Tags $tags
     *
     * @return JsonResponse
     */
    public function removeBlogTag(string $slug)
    {
        $tag = $this->requestParser->getTagBySlug($slug);

        Assert::notNull($tag);

        $this->tagHandler->removeFromBlog($tag);

        return $this->json([]);
    }
}
