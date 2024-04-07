<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Controller\ControllerTrait;
use App\Entity\ProductColor;
use App\Entity\ProductSize;
use App\Entity\Tags;
use App\Handler\ProductColorHandler;
use App\Handler\TagHandler;
use App\Handler\SizeHandler;
use App\Parser\ColorRequestParser;
use App\Parser\SizeRequestParser;
use App\Parser\TagRequestParser;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SizeEditController extends AbstractController
{
    use ControllerTrait;

    private \App\Parser\SizeRequestParser $requestParser;

    private \App\Handler\SizeHandler $sizeHandler;
    private \Symfony\Contracts\Translation\TranslatorInterface $translator;

    /**
     * SizeEditController constructor.
     */
    public function __construct(
        SizeRequestParser $requestParser,
        SizeHandler $sizeHandler,
        TranslatorInterface $translator
    ) {
        $this->requestParser = $requestParser;
        $this->sizeHandler = $sizeHandler;
        $this->translator = $translator;
    }

    /**
     *
     *
     * @return JsonResponse
     * @throws \Exception
     */
    #[Route(path: '/api/add-size', name: 'admin.add_size_api', methods: ['POST'], options: ['expose' => true])]
    public function insert(Request $request)
    {
        $size = $this->requestParser->parse($request->request);

        $this->sizeHandler->save($size);

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
    #[Route(path: '/api/edit-size/{slug}', name: 'admin.edit_size_api', methods: ['PUT'], options: ['expose' => true])]
    public function update(Request $request, ProductSize $productSize)
    {
        $productSize = $this->requestParser->parse($request->request, $productSize);

        $this->sizeHandler->save($productSize);

        $request->getSession()->getFlashBag()->add('message', $this->translator->trans('data.success_send'));

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }
}