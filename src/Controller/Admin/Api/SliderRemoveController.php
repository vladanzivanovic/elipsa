<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\ProductColor;
use App\Entity\Tags;
use App\Entity\Slider;
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

final class SliderRemoveController extends AbstractController
{
    private \App\Handler\SliderHandler $sliderHandler;

    /**
     * TagRemoveController constructor.
     *
     * @param ProductHasTagsRepository $hasTagsRepository
     */
    public function __construct(
        SliderHandler $sliderHandler
    ) {
        $this->sliderHandler = $sliderHandler;
    }

    /**
     *
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    #[Route(path: '/api/remove-slider/{id}', name: 'admin.remove_slider_api', methods: ['DELETE'], options: ['expose' => true])]
    public function remove(Slider $slider): JsonResponse
    {
        $this->sliderHandler->remove($slider);

        return $this->json(null);
    }
}