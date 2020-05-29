<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Formatter\Admin\BlogDataTableResponseFormatter;
use App\Parser\DataTableRequestParser;
use App\Repository\BlogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class BlogListController extends AbstractController
{
    /**
     * @var DataTableRequestParser
     */
    private $requestParser;
    /**
     * @var BlogRepository
     */
    private $blogRepository;
    /**
     * @var BlogDataTableResponseFormatter
     */
    private $responseFormatter;

    /**
     * BlogListController constructor.
     *
     * @param DataTableRequestParser         $requestParser
     * @param BlogRepository                 $blogRepository
     * @param BlogDataTableResponseFormatter $responseFormatter
     */
    public function __construct(
        DataTableRequestParser $requestParser,
        BlogRepository $blogRepository,
        BlogDataTableResponseFormatter $responseFormatter
    ) {
        $this->requestParser = $requestParser;
        $this->blogRepository = $blogRepository;
        $this->responseFormatter = $responseFormatter;
    }

    /**
     * @Route("/api/get-blog-list", name="admin.blog_list", methods={"POST"}, options={"expose": true})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function getList(Request $request): JsonResponse
    {
        $formattedRequest = $this->requestParser->formatRequest($request);
        $total = $this->blogRepository->countBlog();

        $data = $this->blogRepository->getListForAdmin($formattedRequest);

        $response = $this->responseFormatter->formatResponse($formattedRequest, $data, (int)$total);

        return $this->json($response);
    }
}