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

    private \App\Parser\CategoryRequestParser $requestParser;

    private \App\Handler\CategoryHandler $categoryHandler;
    private \Symfony\Contracts\Translation\TranslatorInterface $translator;

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
     *
     *
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[Route(path: '/api/add-category', name: 'admin.add_category_api', methods: ['POST'], options: ['expose' => true])]
    public function insert(Request $request)
    {
        $category = $this->requestParser->parse($request->request);

        $this->categoryHandler->save($category);

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
    #[Route(path: '/api/edit-category/{slug}', name: 'admin.edit_category_api', methods: ['PUT'], options: ['expose' => true])]
    public function update(Request $request, CategoryTranslation $categoryTranslation)
    {
        $category = $this->requestParser->parse($request->request, $categoryTranslation->getCategory());

        $this->categoryHandler->save($category, true);

        $request->getSession()->getFlashBag()->add('message', $this->translator->trans('data.success_send'));

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    
    #[Route(path: '/api/category-change-home-page/{slug}/{status}', name: 'admin.api_category_change_home_page', methods: ['PATCH'], options: ['expose' => true])]
    public function toggleHomePage(CategoryTranslation $categoryTranslation, int $status): JsonResponse
    {
        $this->categoryHandler->toggleHomePage($categoryTranslation->getCategory(), (bool) $status);

        return $this->json(null);
    }
}