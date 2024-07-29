<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Formatter\Admin\CollaboratorDataTableResponseFormatter;
use App\Parser\DataTableRequestParser;
use App\Repository\CollaboratorRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CollaboratorListController extends AbstractController
{
    private \App\Parser\DataTableRequestParser $requestParser;

    private \App\Formatter\Admin\CollaboratorDataTableResponseFormatter $responseFormatter;

    private \App\Repository\CollaboratorRepository $repository;

    public function __construct(
        DataTableRequestParser $requestParser,
        CollaboratorDataTableResponseFormatter $responseFormatter,
        CollaboratorRepository $repository
    ) {
        $this->requestParser = $requestParser;
        $this->responseFormatter = $responseFormatter;
        $this->repository = $repository;
    }

    /**
     *
     *
     * @return JsonResponse
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route(path: '/api/get-collaborator-list', name: 'admin.get_collaborator_list', methods: ['POST'], options: ['expose' => true])]
    public function getList(Request $request)
    {
        $formattedRequest = $this->requestParser->formatRequest($request);
        $total = $this->repository->countData();

        $data = $this->repository->getAdminList($formattedRequest);

        $response = $this->responseFormatter->formatResponse($formattedRequest, $data, (int)$total);

        return new JsonResponse($response);
    }
}
