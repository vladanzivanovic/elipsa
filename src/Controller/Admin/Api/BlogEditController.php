<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Request\Dto\Admin\BlogEditRequestDto;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use ReflectionException;
use App\Entity\Blog;
use App\Handler\BlogHandler;
use App\Helper\ConstantsHelper;
use App\Parser\BlogRequestParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class BlogEditController extends AbstractController
{
    private BlogRequestParser $blogRequestParser;

    private BlogHandler $blogHandler;

    public function __construct(
        BlogRequestParser $blogRequestParser,
        BlogHandler $blogHandler
    ) {
        $this->blogRequestParser = $blogRequestParser;
        $this->blogHandler = $blogHandler;
    }

    /**
     *
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[Route(path: '/api/blog/add', name: 'admin.add_blog_api', options: ['expose' => true], methods: ['POST'])]
    public function insert(BlogEditRequestDto $blogEditRequestDto): JsonResponse
    {
        $blog = $this->blogRequestParser->parse($blogEditRequestDto);

        $this->blogHandler->save($blog);

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     *
     * @throws ORMException
     */
    #[Route(path: '/api/blog/{id}', name: 'admin.edit_blog_api', options: ['expose' => true], methods: ['PUT'])]
    public function edit(Blog $blog, BlogEditRequestDto $blogEditRequestDto): JsonResponse
    {
        $this->blogRequestParser->parse($blogEditRequestDto, $blog);

        $this->blogHandler->save($blog);

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     *
     * @throws ReflectionException
     */
    #[Route(path: '/api/blog/status/{status}/{id}', name: 'admin.set_blog_status_api', options: ['expose' => true], methods: ['PATCH'])]
    public function changeStatus(Blog $blog, int $status): JsonResponse
    {
        $blog->setStatus($status);

        $this->blogHandler->save($blog);

        $statusText = ConstantsHelper::getConstantName((string) $status, 'STATUS', Blog::class);

        return $this->json(['text' => $statusText], Response::HTTP_CREATED);
    }
}
