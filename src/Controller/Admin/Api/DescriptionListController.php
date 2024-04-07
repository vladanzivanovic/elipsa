<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Formatter\Admin\DescriptionDataTableResponseFormatter;
use App\Parser\DataTableRequestParser;
use App\Repository\DescriptionRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class DescriptionListController extends AbstractController
{
    private \App\Parser\DataTableRequestParser $requestParser;

    private \App\Formatter\Admin\DescriptionDataTableResponseFormatter $responseFormatter;

    private \App\Repository\DescriptionRepository $descriptionRepository;

    public function __construct(
        DataTableRequestParser $requestParser,
        DescriptionRepository $descriptionRepository,
        DescriptionDataTableResponseFormatter $responseFormatter
    ) {
        $this->requestParser = $requestParser;
        $this->responseFormatter = $responseFormatter;
        $this->descriptionRepository = $descriptionRepository;
    }

    /**
     *
     *
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route(path: '/api/get-description-list', name: 'admin.get_description_list', methods: ['POST'], options: ['expose' => true])]
    public function getList(Request $request): JsonResponse
    {
        $formattedRequest = $this->requestParser->formatRequest($request);
        $total = $this->descriptionRepository->countData();

        $data = $this->descriptionRepository->getAdminList($formattedRequest);

        $response = $this->responseFormatter->formatResponse($formattedRequest, $data, (int)$total);

        return new JsonResponse($response);
    }
}