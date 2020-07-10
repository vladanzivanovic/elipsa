<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Formatter\Admin\CategoryDataTableResponseFormatter;
use App\Formatter\Admin\ProductColorDataTableResponseFormatter;
use App\Formatter\Admin\ProductDataTableResponseFormatter;
use App\Parser\DataTableRequestParser;
use App\Repository\CategoryRepository;
use App\Repository\ProductColorRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryListController extends AbstractController
{
    /**
     * @var DataTableRequestParser
     */
    private $requestParser;

    /**
     * @var CategoryDataTableResponseFormatter
     */
    private $responseFormatter;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @param DataTableRequestParser             $requestParser
     * @param CategoryRepository                 $categoryRepository
     * @param CategoryDataTableResponseFormatter $responseFormatter
     */
    public function __construct(
        DataTableRequestParser $requestParser,
        CategoryRepository $categoryRepository,
        CategoryDataTableResponseFormatter $responseFormatter
    ) {
        $this->requestParser = $requestParser;
        $this->responseFormatter = $responseFormatter;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/api/get-category-list", name="admin.get_category_list", methods={"POST"}, options={"expose": true})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getList(Request $request)
    {
        $formattedRequest = $this->requestParser->formatRequest($request);
        $total = $this->categoryRepository->countData();

        $data = $this->categoryRepository->getAdminList($formattedRequest);

        $response = $this->responseFormatter->formatResponse($formattedRequest, $data, (int)$total);

        return new JsonResponse($response);
    }
}