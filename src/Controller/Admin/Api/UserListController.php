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
use App\Formatter\Admin\UserDataTableResponseFormatter;
use App\Parser\DataTableRequestParser;
use App\Repository\BannerRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\TagsRepository;
use App\Repository\SliderRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class UserListController extends AbstractController
{
    /**
     * @var DataTableRequestParser
     */
    private $requestParser;

    /**
     * @var UserDataTableResponseFormatter
     */
    private $responseFormatter;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param DataTableRequestParser         $requestParser
     * @param UserRepository                 $userRepository
     * @param UserDataTableResponseFormatter $responseFormatter
     */
    public function __construct(
        DataTableRequestParser $requestParser,
        UserRepository $userRepository,
        UserDataTableResponseFormatter $responseFormatter
    ) {
        $this->requestParser = $requestParser;
        $this->responseFormatter = $responseFormatter;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/api/get-user-list", name="admin.get_user_list", methods={"POST"}, options={"expose": true})
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
        $total = $this->userRepository->countData();

        $data = $this->userRepository->getAdminList($formattedRequest);

        $response = $this->responseFormatter->formatResponse($formattedRequest, $data, (int)$total);

        return new JsonResponse($response);
    }
}