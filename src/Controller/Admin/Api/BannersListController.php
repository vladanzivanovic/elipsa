<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Banner;
use App\Entity\Tags;
use App\Formatter\Admin\BannerDataTableResponseFormatter;
use App\Formatter\Admin\ProductColorDataTableResponseFormatter;
use App\Formatter\Admin\ProductDataTableResponseFormatter;
use App\Formatter\Admin\ProductTagDataTableResponseFormatter;
use App\Formatter\Admin\SliderDataTableResponseFormatter;
use App\Parser\DataTableRequestParser;
use App\Repository\BannerRepository;
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

final class BannersListController extends AbstractController
{
    /**
     * @var DataTableRequestParser
     */
    private $requestParser;

    /**
     * @var BannerDataTableResponseFormatter
     */
    private $responseFormatter;

    /**
     * @var BannerRepository
     */
    private $bannerRepository;

    /**
     * @param DataTableRequestParser           $requestParser
     * @param BannerRepository                 $bannerRepository
     * @param BannerDataTableResponseFormatter $responseFormatter
     */
    public function __construct(
        DataTableRequestParser $requestParser,
        BannerRepository $bannerRepository,
        BannerDataTableResponseFormatter $responseFormatter
    ) {
        $this->requestParser = $requestParser;
        $this->responseFormatter = $responseFormatter;
        $this->bannerRepository = $bannerRepository;
    }

    /**
     * @Route("/api/get-home-collection-banner-list", name="admin.get_home_banner_list", methods={"POST"}, options={"expose": true})
     * @Route("/api/get-banner-list", name="admin.get_banner_list", methods={"POST"}, options={"expose": true})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getList(Request $request)
    {
        $collectionType = [
            Banner::TYPE_LOYALTY,
            Banner::TYPE_NEWS_LETTER,
            Banner::TYPE_POP_UP,
        ];

        if ($request->attributes->get('_route') === 'admin.get_home_banner_list') {
            $collectionType = [Banner::TYPE_SPEED_LINKS];
        }

        $formattedRequest = $this->requestParser->formatRequest($request);
        $total = $this->bannerRepository->countData();

        $data = $this->bannerRepository->getAdminList($formattedRequest, $collectionType);

        $response = $this->responseFormatter->formatResponse($formattedRequest, $data, (int)$total);

        return new JsonResponse($response);
    }
}