<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Banner;
use App\Entity\ProductColor;
use App\Entity\ProductTags;
use App\Entity\Slider;
use App\Handler\BannerHandler;
use App\Handler\ProductColorHandler;
use App\Handler\ProductTagHandler;
use App\Handler\SliderHandler;
use App\Repository\ProductHasColorRepository;
use App\Repository\ProductHasTagsRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class BannerRemoveController extends AbstractController
{
    /**
     * @var ProductHasTagsRepository
     */
    private $hasTagsRepository;

    /**
     * @var BannerHandler
     */
    private $bannerHandler;

    /**
     * ProductTagRemoveController constructor.
     *
     * @param BannerHandler            $bannerHandler
     * @param ProductHasTagsRepository $hasTagsRepository
     */
    public function __construct(
        BannerHandler $bannerHandler,
        ProductHasTagsRepository $hasTagsRepository
    ) {
        $this->hasTagsRepository = $hasTagsRepository;
        $this->bannerHandler = $bannerHandler;
    }

    /**
     * @Route("/api/remove-banner/{id}", name="admin.remove_banner_api", methods={"DELETE"}, options={"expose": true})
     *
     * @param Banner $banner
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(Banner $banner): JsonResponse
    {
        $this->bannerHandler->remove($banner);

        return $this->json(null);
    }
}