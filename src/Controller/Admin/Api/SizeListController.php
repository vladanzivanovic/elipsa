<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Formatter\Admin\ProductTagDataTableResponseFormatter;
use App\Parser\DataTableRequestParser;
use App\Repository\ProductSizeRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class SizeListController extends AbstractController
{
    public function __construct(
        private readonly DataTableRequestParser $requestParser,
        private readonly ProductSizeRepository $sizeRepository,
        private readonly ProductTagDataTableResponseFormatter $responseFormatter,
    ) {}

    /**
     * @return JsonResponse
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route(path: '/api/get-size-list', name: 'admin.get_size_list', options: ['expose' => true], methods: ['POST'])]
    public function getList(Request $request)
    {
        $formattedRequest = $this->requestParser->formatRequest($request);
        $total = $this->sizeRepository->countData();

        $data = $this->sizeRepository->getAdminList($formattedRequest);

        $response = $this->responseFormatter->formatResponse($formattedRequest, $data, (int)$total);

        return new JsonResponse($response);
    }
}
