<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\ProductTags;
use App\Formatter\Admin\ProductColorDataTableResponseFormatter;
use App\Formatter\Admin\ProductDataTableResponseFormatter;
use App\Formatter\Admin\ProductTagDataTableResponseFormatter;
use App\Formatter\Admin\SliderDataTableResponseFormatter;
use App\Parser\DataTableRequestParser;
use App\Repository\ProductColorRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\ProductTagsRepository;
use App\Repository\SliderRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class SliderListController extends AbstractController
{
    /**
     * @var DataTableRequestParser
     */
    private $requestParser;

    /**
     * @var SliderDataTableResponseFormatter
     */
    private $responseFormatter;

    /**
     * @var SliderRepository
     */
    private $sliderRepository;

    /**
     * SizeListController constructor.
     *
     * @param DataTableRequestParser               $requestParser
     * @param SliderRepository                     $sliderRepository
     * @param SliderDataTableResponseFormatter     $responseFormatter
     */
    public function __construct(
        DataTableRequestParser $requestParser,
        SliderRepository $sliderRepository,
        SliderDataTableResponseFormatter $responseFormatter
    ) {
        $this->requestParser = $requestParser;
        $this->responseFormatter = $responseFormatter;
        $this->sliderRepository = $sliderRepository;
    }

    /**
     * @Route("/api/get-slider-list", name="admin.get_slider_list", methods={"POST"}, options={"expose": true})
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
        $total = $this->sliderRepository->countData();

        $data = $this->sliderRepository->getAdminList($formattedRequest);

        $response = $this->responseFormatter->formatResponse($formattedRequest, $data, (int)$total);

        return new JsonResponse($response);
    }
}