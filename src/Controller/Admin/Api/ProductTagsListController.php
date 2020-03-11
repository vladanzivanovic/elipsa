<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\ProductTags;
use App\Formatter\Admin\ProductColorDataTableResponseFormatter;
use App\Formatter\Admin\ProductDataTableResponseFormatter;
use App\Formatter\Admin\ProductTagDataTableResponseFormatter;
use App\Parser\DataTableRequestParser;
use App\Repository\ProductColorRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\ProductTagsRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class ProductTagsListController extends AbstractController
{
    /**
     * @var DataTableRequestParser
     */
    private $requestParser;

    /**
     * @var ProductTagDataTableResponseFormatter
     */
    private $responseFormatter;

    /**
     * @var ProductTagsRepository
     */
    private $tagsRepository;

    /**
     * ProductTagsListController constructor.
     *
     * @param DataTableRequestParser               $requestParser
     * @param ProductTagsRepository                $tagsRepository
     * @param ProductTagDataTableResponseFormatter $responseFormatter
     */
    public function __construct(
        DataTableRequestParser $requestParser,
        ProductTagsRepository $tagsRepository,
        ProductTagDataTableResponseFormatter $responseFormatter
    ) {
        $this->requestParser = $requestParser;
        $this->responseFormatter = $responseFormatter;
        $this->tagsRepository = $tagsRepository;
    }

    /**
     * @Route("/api/get-product-tags-list", name="admin.get_product_tags_list", methods={"POST"}, options={"expose": true})
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
        $total = $this->tagsRepository->countData();

        $data = $this->tagsRepository->getAdminList($formattedRequest);

        $response = $this->responseFormatter->formatResponse($formattedRequest, $data, (int)$total);

        return new JsonResponse($response);
    }
}