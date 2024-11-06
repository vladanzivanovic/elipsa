<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Formatter\Admin\OfficeContactDataTableResponseFormatter;
use App\Parser\DataTableRequestParser;
use App\Repository\OfficeContactRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class OfficeContactListController extends AbstractController
{
    public function __construct(
        private readonly DataTableRequestParser $requestParser,
        private readonly OfficeContactRepository $repository,
        private readonly OfficeContactDataTableResponseFormatter $responseFormatter
    ) {}

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route(path: '/api/get-office-contact-list', name: 'admin.get_office_contact_list', options: ['expose' => true], methods: ['POST'])]
    public function getList(Request $request): JsonResponse
    {
        $formattedRequest = $this->requestParser->formatRequest($request);
        $total = $this->repository->countData();

        $data = $this->repository->getAdminList($formattedRequest);

        $response = $this->responseFormatter->formatResponse($formattedRequest, $data, (int)$total);

        return new JsonResponse($response);
    }
}
