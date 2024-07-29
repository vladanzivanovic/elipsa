<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Slider;
use App\Handler\SliderHandler;
use App\Helper\ConstantsHelper;
use App\Parser\SliderEditRequestParser;
use App\Request\Dto\Admin\SliderEditRequestDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SliderEditController extends AbstractController
{
    public function __construct(
        private readonly SliderEditRequestParser $requestParser,
        private readonly SliderHandler $sliderHandler
    ) {}

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    #[Route(path: '/api/app-slider', name: 'admin.add_slider_api', methods: ['POST'])]
    public function insert(SliderEditRequestDto $sliderEditRequestDto): JsonResponse
    {
        $slider = $this->requestParser->parse($sliderEditRequestDto);

        $this->sliderHandler->save($slider);

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    #[Route(path: '/api/set-sliders-position', name: 'admin.set_sliders_position', options: ['expose' => true], methods: ['POST'])]
    public function changeOrder(Request $request): JsonResponse
    {
        $this->sliderHandler->saveRowsPositions($request->request);

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    #[Route(path: '/api/edit-slider/{id}', name: 'admin.edit_slider_api', options: ['expose' => true], methods: ['PUT'])]
    public function update(SliderEditRequestDto $sliderEditRequestDto, Slider $slider): JsonResponse
    {
        $slider = $this->requestParser->parse($sliderEditRequestDto, $slider);

        $this->sliderHandler->save($slider);

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     *
     *
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    #[Route(path: '/api/toggle-slider-status/{id}/{status}', name: 'admin.api_toggle_slider_status', options: ['expose' => true], methods: ['PATCH'])]
    public function toggleActivation(Slider $slider, int $status): JsonResponse
    {
        $slider->setIsActive((bool) $status);

        $this->sliderHandler->save($slider);

        $statusText = ConstantsHelper::getConstantName((string) $status, 'STATUS', Slider::class);

        return $this->json(['text' => $statusText]);
    }
}
