<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Promotion;
use App\Formatter\Admin\PromotionCouponsDataTableResponseFormatter;
use App\Parser\DataTableRequestParser;
use App\Repository\PromotionRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class PromotionsListController extends AbstractController
{
    public function __construct(
        private readonly DataTableRequestParser $requestParser,
        private readonly PromotionRepository $couponsRepository,
        private readonly PromotionCouponsDataTableResponseFormatter $responseFormatter
    ) {}

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route(path: '/api/promotions/list', name: 'admin.get_promotions_list', options: ['expose' => true], methods: ['POST'])]
    public function getCouponsList(Request $request): JsonResponse
    {
        $formattedRequest = $this->requestParser->formatRequest($request);
        $total = $this->couponsRepository->countData();

        $data = $this->couponsRepository->getAdminList($formattedRequest);

        $response = $this->responseFormatter->formatResponse($formattedRequest, $data, (int)$total);

        return new JsonResponse($response);
    }
}
