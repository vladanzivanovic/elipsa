<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Controller\ControllerTrait;
use App\Entity\ProductColor;
use App\Entity\Tags;
use App\Handler\ProductColorHandler;
use App\Handler\TagHandler;
use App\Parser\ColorRequestParser;
use App\Parser\TagRequestParser;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TagEditController extends AbstractController
{
    use ControllerTrait;

    private TagRequestParser $requestParser;

    private TagHandler $tagHandler;

    private TranslatorInterface $translator;

    public function __construct(
        TagRequestParser $requestParser,
        TagHandler $tagHandler,
        TranslatorInterface $translator
    ) {
        $this->requestParser = $requestParser;
        $this->tagHandler = $tagHandler;
        $this->translator = $translator;
    }

    /**
     * @Route("/api/add-product-tag", name="admin.add_product_tag_api", methods={"POST"}, options={"expose": true})
     * @Route("/api/add-blog-tag", name="admin.add_blog_tag_api", methods={"POST"}, options={"expose": true})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function insert(Request $request): JsonResponse
    {
        $relatedType = $request->attributes->get('_route') === 'admin.add_blog_tag_api' ? Tags::TYPE_BLOG : Tags::TYPE_PRODUCT;

        $slug = $this->getRsSlug($request);

        $tags = $this->requestParser->parse($request->request, $slug, $relatedType);

        $this->tagHandler->save($tags);

        $request->getSession()->getFlashBag()->add('message', $this->translator->trans('data.success_send'));

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/edit-product-tag/{slug}", name="admin.edit_product_tag_api", methods={"PUT"}, options={"expose": true})
     * @Route("/api/edit-blog-tag/{slug}", name="admin.edit_blog_tag_api", methods={"PUT"}, options={"expose": true})
     *
     * @param Request $request
     * @param Tags    $productTags
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(Request $request, Tags $productTags): JsonResponse
    {
        $relatedType = $request->attributes->get('_route') === 'admin.edit_blog_tag_api' ? Tags::TYPE_BLOG : Tags::TYPE_PRODUCT;

        $tags = $this->requestParser->parse($request->request, $productTags->getMainSlug(), $relatedType, true);

        $this->tagHandler->save($tags, true);

        $request->getSession()->getFlashBag()->add('message', $this->translator->trans('data.success_send'));

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    private function getRsSlug(Request $request): string
    {
        $slug = Urlizer::transliterate($request->request->get('rs_title'));

        return $slug;
    }
}
