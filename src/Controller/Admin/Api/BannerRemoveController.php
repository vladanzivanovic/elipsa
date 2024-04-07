<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Banner;
use App\Entity\ProductColor;
use App\Entity\Tags;
use App\Entity\Slider;
use App\Handler\BannerHandler;
use App\Handler\ProductColorHandler;
use App\Handler\TagHandler;
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
    private \App\Handler\BannerHandler $bannerHandler;

    public function __construct(
        BannerHandler $bannerHandler
    ) {
        $this->bannerHandler = $bannerHandler;
    }

    /**
     *
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    #[Route(path: '/api/remove-banner/{id}', name: 'admin.remove_banner_api', methods: ['DELETE'], options: ['expose' => true])]
    public function remove(Banner $banner): JsonResponse
    {
        $this->bannerHandler->remove($banner);

        return $this->json(null);
    }
}