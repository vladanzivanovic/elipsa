<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Controller\ControllerTrait;
use App\Entity\ProductColor;
use App\Handler\ProductColorHandler;
use App\Parser\ColorRequestParser;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProductColorEditController extends AbstractController
{
    use ControllerTrait;

    /**
     * @var ColorRequestParser
     */
    private $requestParser;

    /**
     * @var ProductColorHandler
     */
    private $colorHandler;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * ProductColorEditController constructor.
     *
     * @param ColorRequestParser  $requestParser
     * @param ProductColorHandler $colorHandler
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ColorRequestParser $requestParser,
        ProductColorHandler $colorHandler,
        TranslatorInterface $translator
    ) {
        $this->requestParser = $requestParser;
        $this->colorHandler = $colorHandler;
        $this->translator = $translator;
    }

    /**
     * @Route("/api/add-color", name="admin.add_color_api", methods={"POST"}, options={"expose": true})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function insert(Request $request)
    {
        $slug = Urlizer::transliterate($request->request->get('rs_title'));

        $colors = $this->requestParser->parse($request->request, $slug);

        $this->colorHandler->save($colors);

        $request->getSession()->getFlashBag()->add('message', $this->translator->trans('data.success_send'));

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/edit-color/{slug}", name="admin.edit_color_api", methods={"PUT"}, options={"expose": true})
     *
     * @param Request      $request
     * @param ProductColor $productColor
     *
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function update(Request $request, ProductColor $productColor)
    {
        $slug = Urlizer::transliterate($request->request->get('rs_title'));

        $colors = $this->requestParser->parse($request->request, $productColor->getMainSlug(), true);

        $this->colorHandler->save($colors, true);

        $request->getSession()->getFlashBag()->add('message', $this->translator->trans('data.success_send'));

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }
}