<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Formatter\Admin\CareerDataTableResponseFormatter;
use App\Parser\DataTableRequestParser;
use App\Repository\CareerRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CareerListController extends AbstractController
{
    /**
     * @var DataTableRequestParser
     */
    private $requestParser;

    /**
     * @var CareerDataTableResponseFormatter
     */
    private $responseFormatter;

    /**
     * @var CareerRepository
     */
    private $repository;

    /**
     * @param DataTableRequestParser           $requestParser
     * @param CareerDataTableResponseFormatter $responseFormatter
     * @param CareerRepository                 $repository
     */
    public function __construct(
        DataTableRequestParser $requestParser,
        CareerDataTableResponseFormatter $responseFormatter,
        CareerRepository $repository
    ) {
        $this->requestParser = $requestParser;
        $this->responseFormatter = $responseFormatter;
        $this->repository = $repository;
    }

    /**
     * @Route("/api/get-career-list", name="admin.get_career_list", methods={"POST"}, options={"expose": true})
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