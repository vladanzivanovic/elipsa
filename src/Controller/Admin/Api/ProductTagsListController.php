<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Tags;
use App\Formatter\Admin\ProductColorDataTableResponseFormatter;
use App\Formatter\Admin\ProductDataTableResponseFormatter;
use App\Formatter\Admin\ProductTagDataTableResponseFormatter;
use App\Parser\DataTableRequestParser;
use App\Repository\ProductColorRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\TagsRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class ProductTagsListController extends AbstractController
{
    private DataTableRequestParser $requestParser;

    private ProductTagDataTableResponseFormatter $responseFormatter;

    private TagsRepository $tagsRepository;

    public function __construct(
        DataTableRequestParser $requestParser,
        TagsRepository $tagsRepository,
        ProductTagDataTableResponseFormatter $responseFormatter
    ) {
        $this->requestParser = $requestParser;
        $this->responseFormatter = $responseFormatter;
        $this->tagsRepository = $tagsRepository;
    }

    /**
     * @Route("/api/get-product-tags-list", name="admin.get_product_tags_list", methods={"POST"}, options={"expose": true})
     * @Route("/api/get-blog-tags-list", name="admin.get_blog_tags_list", methods={"POST"}, options={"expose": true})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getList(Request $request)
    {
        $relatedType = $request->attributes->get('_route') === 'admin.get_blog_tags_list' ? Tags::TYPE_BLOG : Tags::TYPE_PRODUCT;

        $formattedRequest = $this->requestParser->formatRequest($request);

        $total = $this->tagsRepository->countData($relatedType);

        $data = $this->tagsRepository->getAdminList($formattedRequest, $relatedType);

        $response = $this->responseFormatter->formatResponse($formattedRequest, $data, (int)$total);

        return new JsonResponse($response);
    }
}
