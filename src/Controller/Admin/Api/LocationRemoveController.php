<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Banner;
use App\Entity\Location;
use App\Entity\ProductColor;
use App\Entity\Tags;
use App\Entity\Slider;
use App\Handler\BannerHandler;
use App\Handler\LocationHandler;
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

final class LocationRemoveController extends AbstractController
{
    /**
     * @var LocationHandler
     */
    private $locationHandler;

    /**
     * @param LocationHandler $locationHandler
     */
    public function __construct(
        LocationHandler $locationHandler
    ) {
        $this->locationHandler = $locationHandler;
    }

    /**
     * @Route("/api/remove-location/{id}", name="admin.remove_location_api", methods={"DELETE"}, options={"expose": true})
     *
     * @param Location $location
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(Location $location): JsonResponse
    {
        $this->locationHandler->remove($location);

        return $this->json(null);
    }
}