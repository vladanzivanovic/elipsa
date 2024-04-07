<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Controller\ControllerTrait;
use App\Entity\ProductColor;
use App\Handler\ProductColorHandler;
use App\Parser\ColorRequestParser;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProductColorEditController extends AbstractController
{
    use ControllerTrait;

    private \App\Parser\ColorRequestParser $requestParser;

    private \App\Handler\ProductColorHandler $colorHandler;
    private \Symfony\Contracts\Translation\TranslatorInterface $translator;

    /**
     * ProductColorEditController constructor.
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
     *
     *
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[Route(path: '/api/add-color', name: 'admin.add_color_api', methods: ['POST'], options: ['expose' => true])]
    public function insert(Request $request)
    {
        $productColor = $this->requestParser->parse($request->request);

        $this->colorHandler->save($productColor);

        $request->getSession()->getFlashBag()->add('message', $this->translator->trans('data.success_send'));

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    /**
     *
     *
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[Route(path: '/api/edit-color/{id}', name: 'admin.edit_color_api', methods: ['PUT'], options: ['expose' => true])]
    public function update(Request $request, ProductColor $productColor)
    {
        $productColor = $this->requestParser->parse($request->request, $productColor);

        $this->colorHandler->save($productColor);

        $request->getSession()->getFlashBag()->add('message', $this->translator->trans('data.success_send'));

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }
}