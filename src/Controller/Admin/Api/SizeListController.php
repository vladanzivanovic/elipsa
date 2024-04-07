<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Tags;
use App\Formatter\Admin\ProductColorDataTableResponseFormatter;
use App\Formatter\Admin\ProductDataTableResponseFormatter;
use App\Formatter\Admin\ProductTagDataTableResponseFormatter;
use App\Parser\DataTableRequestParser;
use App\Repository\ProductColorRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\TagsRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class SizeListController extends AbstractController
{
    private \App\Parser\DataTableRequestParser $requestParser;

    private \App\Formatter\Admin\ProductTagDataTableResponseFormatter $responseFormatter;

    private \App\Repository\ProductSizeRepository $sizeRepository;

    /**
     * SizeListController constructor.
     */
    public function __construct(
        DataTableRequestParser $requestParser,
        ProductSizeRepository $sizeRepository,
        ProductTagDataTableResponseFormatter $responseFormatter
    ) {
        $this->requestParser = $requestParser;
        $this->responseFormatter = $responseFormatter;
        $this->sizeRepository = $sizeRepository;
    }

    /**
     *
     *
     * @return JsonResponse
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route(path: '/api/get-size-list', name: 'admin.get_size_list', methods: ['POST'], options: ['expose' => true])]
    public function getList(Request $request)
    {
        $formattedRequest = $this->requestParser->formatRequest($request);
        $total = $this->sizeRepository->countData();

        $data = $this->sizeRepository->getAdminList($formattedRequest);

        $response = $this->responseFormatter->formatResponse($formattedRequest, $data, (int)$total);

        return new JsonResponse($response);
    }
}