<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\CategoryTranslation;
use App\Handler\CategoryHandler;
use App\Parser\CategoryRequestParser;
use App\Request\Dto\Admin\CategoryEditRequestDto;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CategoryEditController extends AbstractController
{
    public function __construct(
        private readonly CategoryRequestParser $requestParser,
        private readonly CategoryHandler $categoryHandler,
        private readonly TranslatorInterface $translator
    ) {}

    /**
     *
     *
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[Route(path: '/api/add-category', name: 'admin.add_category_api', options: ['expose' => true], methods: ['POST'])]
    public function insert(CategoryEditRequestDto $categoryEditRequestDto)
    {
        $category = $this->requestParser->parse($categoryEditRequestDto);

        $this->categoryHandler->save($category);

        $categoryEditRequestDto->session->getFlashBag()->add('message', $this->translator->trans('data.success_send'));

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     *
     *
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[Route(path: '/api/edit-category/{slug}', name: 'admin.edit_category_api', options: ['expose' => true], methods: ['PUT'])]
    public function update(CategoryEditRequestDto $categoryEditRequestDto, CategoryTranslation $categoryTranslation): JsonResponse
    {
        $category = $this->requestParser->parse($categoryEditRequestDto, $categoryTranslation->getCategory());

        $this->categoryHandler->save($category, true);

        $categoryEditRequestDto->session->getFlashBag()->add('message', $this->translator->trans('data.success_send'));

        return $this->json(null, Response::HTTP_CREATED);
    }

    
    #[Route(path: '/api/category-change-home-page/{slug}/{status}', name: 'admin.api_category_change_home_page', options: ['expose' => true], methods: ['PATCH'])]
    public function toggleHomePage(CategoryTranslation $categoryTranslation, int $status): JsonResponse
    {
        $this->categoryHandler->toggleHomePage($categoryTranslation->getCategory(), (bool) $status);

        return $this->json(null);
    }
}
