<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Banner;
use App\Entity\Slider;
use App\Handler\BannerHandler;
use App\Helper\ConstantsHelper;
use App\Parser\BannerEditRequestParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class BannerEditController extends AbstractController
{
    /**
     * @var BannerEditRequestParser
     */
    private $requestParser;

    /**
     * @var BannerHandler
     */
    private $bannerHandler;

    /**
     * @param BannerEditRequestParser $requestParser
     * @param BannerHandler           $bannerHandler
     */
    public function __construct(
        BannerEditRequestParser $requestParser,
        BannerHandler $bannerHandler
    ) {
        $this->requestParser = $requestParser;
        $this->bannerHandler = $bannerHandler;
    }

    /**
     * @Route("/api/add-banner", name="admin.add_banner_api", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    public function insert(Request $request): JsonResponse
    {
        $banner = $this->requestParser->parse($request->request);

        $this->bannerHandler->save($banner);

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/edit-banner/{id}", name="admin.edit_banner_api", methods={"PUT"}, options={"expose": true})
     * @param Request $request
     * @param Banner  $banner
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     */
    public function update(Request $request, Banner $banner): JsonResponse
    {
        $banner = $this->requestParser->parse($request->request, $banner);

        $this->bannerHandler->save($banner);

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/toggle-banner-status/{id}/{status}", name="admin.api_toggle_banner_status", methods={"PATCH"},
     *                                                   options={"expose": true})
     *
     * @param Banner $banner
     * @param int    $status
     *
     * @return JsonResponse
     *
     * @throws \ReflectionException
     */
    public function toggleActivation(Banner $banner, int $status): JsonResponse
    {
        $banner->setIsActive((bool) $status);

        $this->bannerHandler->save($banner);

        $statusText = ConstantsHelper::getConstantName((string) $status, 'STATUS', Slider::class);

        return $this->json(['text' => $statusText]);
    }
}