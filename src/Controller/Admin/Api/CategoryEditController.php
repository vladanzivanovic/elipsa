<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Controller\ControllerTrait;
use App\Entity\CategoryTranslation;
use App\Handler\CategoryHandler;
use App\Parser\CategoryRequestParser;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CategoryEditController extends AbstractController
{
    use ControllerTrait;

    /**
     * @var CategoryRequestParser
     */
    private $requestParser;

    /**
     * @var CategoryHandler
     */
    private $categoryHandler;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * CategoryEditController constructor.
     *
     * @param CategoryRequestParser $requestParser
     * @param CategoryHandler       $categoryHandler
     * @param TranslatorInterface   $translator
     */
    public function __construct(
        CategoryRequestParser $requestParser,
        CategoryHandler $categoryHandler,
        TranslatorInterface $translator
    ) {
        $this->requestParser = $requestParser;
        $this->categoryHandler = $categoryHandler;
        $this->translator = $translator;
    }

    /**
     * @Route("/api/add-category", name="admin.add_category_api", methods={"POST"}, options={"expose": true})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function insert(Request $request)
    {
        $category = $this->requestParser->parse($request->request);

        $this->categoryHandler->save($category);

        $request->getSession()->getFlashBag()->add('message', $this->translator->trans('data.success_send'));

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/edit-category/{slug}", name="admin.edit_category_api", methods={"PUT"}, options={"expose": true})
     *
     * @param Request             $request
     * @param CategoryTranslation $categoryTranslation
     *
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function update(Request $request, CategoryTranslation $categoryTranslation)
    {
        $category = $this->requestParser->parse($request->request, $categoryTranslation->getCategory());

        $this->categoryHandler->save($category, true);

        $request->getSession()->getFlashBag()->add('message', $this->translator->trans('data.success_send'));

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }
}