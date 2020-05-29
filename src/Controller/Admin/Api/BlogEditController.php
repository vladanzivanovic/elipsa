<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use ReflectionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Blog;
use App\Entity\BlogTranslation;
use App\Handler\BlogHandler;
use App\Helper\ConstantsHelper;
use App\Parser\BlogRequestParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogEditController extends AbstractController
{
    /**
     * @var BlogRequestParser
     */
    private $blogRequestParser;
    /**
     * @var BlogHandler
     */
    private $blogHandler;

    /**
     * BlogEditController constructor.
     *
     * @param BlogRequestParser $blogRequestParser
     * @param BlogHandler       $blogHandler
     */
    public function __construct(
        BlogRequestParser $blogRequestParser,
        BlogHandler $blogHandler
    ) {
        $this->blogRequestParser = $blogRequestParser;
        $this->blogHandler = $blogHandler;
    }

    /**
     * @Route("/api/blog-create", name="admin.add_blog_api", methods={"POST"}, options={"expose": true})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function insert(Request $request): JsonResponse
    {
        $blog = $this->blogRequestParser->parse($request->request);

        $this->blogHandler->save($blog);

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/blog-edit/{id}", name="admin.edit_blog_api", methods={"PUT"}, options={"expose": true})
     *
     * @param Blog    $blog
     * @param Request $request
     *
     * @return JsonResponse
     * @throws ORMException
     */
    public function edit(Blog $blog, Request $request): JsonResponse
    {
        $this->blogRequestParser->parse($request->request, $blog);

        $this->blogHandler->save($blog);

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/blog-set-status/{status}/{id}", name="admin.set_blog_status_api", methods={"PATCH"},
     *                                                      options={"expose": true})
     *
     * @param Blog $blog
     * @param int  $status
     *
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function changeStatus(Blog $blog, int $status): JsonResponse
    {
        $blog->setStatus($status);

        $this->blogHandler->save($blog);

        $statusText = ConstantsHelper::getConstantName((string) $status, 'STATUS', Blog::class);

        return $this->json(['text' => $statusText], JsonResponse::HTTP_CREATED);
    }
}