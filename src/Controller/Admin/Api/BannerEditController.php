<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Banner;
use App\Entity\Slider;
use App\Handler\BannerHandler;
use App\Helper\ConstantsHelper;
use App\Parser\BannerEditRequestParser;
use App\Request\Dto\Admin\BannerEditRequestDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BannerEditController extends AbstractController
{
    public function __construct(
        private readonly BannerEditRequestParser $requestParser,
        private readonly BannerHandler $bannerHandler,
    ) {}

    #[Route(path: '/api/add-banner', name: 'admin.add_banner_api', methods: ['POST'])]
    public function insert(BannerEditRequestDto $bannerEditRequestDto): JsonResponse
    {
        $banner = $this->requestParser->parse($bannerEditRequestDto);

        $this->bannerHandler->save($banner);

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     */
    #[Route(path: '/api/edit-banner/{id}', name: 'admin.edit_banner_api', options: ['expose' => true], methods: ['PUT'])]
    public function update(BannerEditRequestDto $bannerEditRequestDto, Banner $banner): JsonResponse
    {
        $banner = $this->requestParser->parse($bannerEditRequestDto, $banner);

        $this->bannerHandler->save($banner);

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     *
     *
     * @throws \ReflectionException
     */
    #[Route(path: '/api/toggle-banner-status/{id}/{status}', name: 'admin.api_toggle_banner_status', options: ['expose' => true], methods: ['PATCH'])]
    public function toggleActivation(Banner $banner, int $status): JsonResponse
    {
        $banner->setIsActive((bool) $status);

        $this->bannerHandler->save($banner);

        $statusText = ConstantsHelper::getConstantName((string) $status, 'STATUS', Slider::class);

        return $this->json(['text' => $statusText]);
    }
}
