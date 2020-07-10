<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Tags;
use App\Formatter\Admin\BannerDataTableResponseFormatter;
use App\Formatter\Admin\LocationDataTableResponseFormatter;
use App\Formatter\Admin\ProductColorDataTableResponseFormatter;
use App\Formatter\Admin\ProductDataTableResponseFormatter;
use App\Formatter\Admin\ProductTagDataTableResponseFormatter;
use App\Formatter\Admin\SliderDataTableResponseFormatter;
use App\Parser\DataTableRequestParser;
use App\Repository\BannerRepository;
use App\Repository\LocationRepository;
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

final class LocationListController extends AbstractController
{
    /**
     * @var DataTableRequestParser
     */
    private $requestParser;

    /**
     * @var LocationDataTableResponseFormatter
     */
    private $responseFormatter;

    /**
     * @var LocationRepository
     */
    private $locationRepository;

    /**
     * @param DataTableRequestParser             $requestParser
     * @param LocationRepository                 $locationRepository
     * @param LocationDataTableResponseFormatter $responseFormatter
     */
    public function __construct(
        DataTableRequestParser $requestParser,
        LocationRepository $locationRepository,
        LocationDataTableResponseFormatter $responseFormatter
    ) {
        $this->requestParser = $requestParser;
        $this->responseFormatter = $responseFormatter;
        $this->locationRepository = $locationRepository;
    }

    /**
     * @Route("/api/get-location-list", name="admin.get_location_list", methods={"POST"}, options={"expose": true})
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
        $total = $this->locationRepository->countData();

        $data = $this->locationRepository->getAdminList($formattedRequest);

        $response = $this->responseFormatter->formatResponse($formattedRequest, $data, (int)$total);

        return new JsonResponse($response);
    }
}