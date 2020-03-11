<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Controller\ControllerTrait;
use App\Entity\ProductColor;
use App\Entity\ProductTags;
use App\Handler\ProductColorHandler;
use App\Handler\ProductTagHandler;
use App\Parser\ColorRequestParser;
use App\Parser\TagRequestParser;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProductTagEditController extends AbstractController
{
    use ControllerTrait;

    /**
     * @var TagRequestParser
     */
    private $requestParser;

    /**
     * @var ProductTagHandler
     */
    private $tagHandler;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * ProductTagEditController constructor.
     *
     * @param TagRequestParser    $requestParser
     * @param ProductTagHandler   $tagHandler
     * @param TranslatorInterface $translator
     */
    public function __construct(
        TagRequestParser $requestParser,
        ProductTagHandler $tagHandler,
        TranslatorInterface $translator
    ) {
        $this->requestParser = $requestParser;
        $this->tagHandler = $tagHandler;
        $this->translator = $translator;
    }

    /**
     * @Route("/api/add-tag", name="admin.add_tag_api", methods={"POST"}, options={"expose": true})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function insert(Request $request)
    {
        $slug = $this->getRsSlug($request);

        $tags = $this->requestParser->parse($request->request, $slug);

        $this->tagHandler->save($tags);

        $request->getSession()->getFlashBag()->add('message', $this->translator->trans('data.success_send'));

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/edit-tag/{slug}", name="admin.edit_tag_api", methods={"PUT"}, options={"expose": true})
     *
     * @param Request     $request
     * @param ProductTags $productTags
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(Request $request, ProductTags $productTags)
    {
        $tags = $this->requestParser->parse($request->request, $productTags->getMainSlug(), true);

        $this->tagHandler->save($tags, true);

        $request->getSession()->getFlashBag()->add('message', $this->translator->trans('data.success_send'));

        return $this->json(null, JsonResponse::HTTP_CREATED);
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