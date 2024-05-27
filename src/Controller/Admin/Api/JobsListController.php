<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Banner;
use App\Entity\Tags;
use App\Formatter\Admin\BannerDataTableResponseFormatter;
use App\Formatter\Admin\CareerDataTableResponseFormatter;
use App\Formatter\Admin\JobsDataTableResponseFormatter;
use App\Formatter\Admin\ProductColorDataTableResponseFormatter;
use App\Formatter\Admin\ProductDataTableResponseFormatter;
use App\Formatter\Admin\ProductTagDataTableResponseFormatter;
use App\Formatter\Admin\SliderDataTableResponseFormatter;
use App\Parser\DataTableRequestParser;
use App\Repository\BannerRepository;
use App\Repository\CareerDescriptionRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\TagsRepository;
use App\Repository\SliderRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class JobsListController extends AbstractController
{
    private \App\Parser\DataTableRequestParser $requestParser;

    private \App\Formatter\Admin\JobsDataTableResponseFormatter $responseFormatter;

    private \App\Repository\CareerDescriptionRepository $careerDescriptionRepository;

    public function __construct(
        DataTableRequestParser $requestParser,
        CareerDescriptionRepository $careerDescriptionRepository,
        JobsDataTableResponseFormatter $responseFormatter
    ) {
        $this->requestParser = $requestParser;
        $this->responseFormatter = $responseFormatter;
        $this->careerDescriptionRepository = $careerDescriptionRepository;
    }

    /**
     *
     *
     * @return JsonResponse
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route(path: '/api/get-jobs-list', name: 'admin.get_jobs_list', options: ['expose' => true], methods: ['POST'])]
    public function getList(Request $request): JsonResponse
    {
        $formattedRequest = $this->requestParser->formatRequest($request);
        $total = $this->careerDescriptionRepository->countData();

        $data = $this->careerDescriptionRepository->getAdminList($formattedRequest);

        $response = $this->responseFormatter->formatResponse($formattedRequest, $data, (int)$total);

        return $this->json($response);
    }
}
