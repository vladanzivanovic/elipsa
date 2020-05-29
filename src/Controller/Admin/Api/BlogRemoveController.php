<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Blog;
use App\Handler\BlogHandler;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class BlogRemoveController extends AbstractController
{
    /**
     * @var BlogHandler
     */
    private $blogHandler;

    /**
     * @param BlogHandler $blogHandler
     */
    public function __construct(
        BlogHandler $blogHandler
    ) {
        $this->blogHandler = $blogHandler;
    }

    /**
     * @Route("/api/blog-remove/{id}", name="admin.remove_blog_api", methods={"DELETE"}, options={"expose": true})
     *
     * @param Blog $blog
     *
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Blog $blog): JsonResponse
    {
        $this->blogHandler->remove($blog);

        return $this->json(null);
    }
}