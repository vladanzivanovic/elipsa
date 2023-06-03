<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Formatter\Admin\OfficeContactDataTableResponseFormatter;
use App\Formatter\Admin\SliderTextDataTableResponseFormatter;
use App\Parser\DataTableRequestParser;
use App\Repository\OfficeContactRepository;
use App\Repository\SliderTextRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class OfficeContactListController extends AbstractController
{
    private DataTableRequestParser $requestParser;

    private OfficeContactDataTableResponseFormatter $responseFormatter;

    private OfficeContactRepository $repository;

    public function __construct(
        DataTableRequestParser $requestParser,
        OfficeContactRepository $repository,
        OfficeContactDataTableResponseFormatter $responseFormatter
    ) {
        $this->requestParser = $requestParser;
        $this->responseFormatter = $responseFormatter;
        $this->repository = $repository;
    }

    /**
     * @Route("/api/get-office-contact-list", name="admin.get_office_contact_list", methods={"POST"}, options={"expose": true})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getList(Request $request): JsonResponse
    {
        $formattedRequest = $this->requestParser->formatRequest($request);
        $total = $this->repository->countData();

        $data = $this->repository->getAdminList($formattedRequest);

        $response = $this->responseFormatter->formatResponse($formattedRequest, $data, (int)$total);

        return new JsonResponse($response);
    }
}
