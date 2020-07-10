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
use Symfony\Component\Routing\Annotation\Route;

final class CollaboratorListController extends AbstractController
{
    /**
     * @var DataTableRequestParser
     */
    private $requestParser;

    /**
     * @var CollaboratorDataTableResponseFormatter
     */
    private $responseFormatter;

    /**
     * @var CollaboratorRepository
     */
    private $repository;

    /**
     * @param DataTableRequestParser                 $requestParser
     * @param CollaboratorDataTableResponseFormatter $responseFormatter
     * @param CollaboratorRepository                 $repository
     */
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
     * @Route("/api/get-collaborator-list", name="admin.get_collaborator_list", methods={"POST"}, options={"expose": true})
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
        $total = $this->repository->countData();

        $data = $this->repository->getAdminList($formattedRequest);

        $response = $this->responseFormatter->formatResponse($formattedRequest, $data, (int)$total);

        return new JsonResponse($response);
    }
}