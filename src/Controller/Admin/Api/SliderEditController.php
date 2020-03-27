<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Slider;
use App\Handler\SliderHandler;
use App\Helper\ConstantsHelper;
use App\Parser\SliderEditRequestParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class SliderEditController extends AbstractController
{
    /**
     * @var SliderEditRequestParser
     */
    private $requestParser;
    /**
     * @var SliderHandler
     */
    private $sliderHandler;

    /**
     * SliderEditController constructor.
     *
     * @param SliderEditRequestParser $requestParser
     * @param SliderHandler           $sliderHandler
     */
    public function __construct(
        SliderEditRequestParser $requestParser,
        SliderHandler $sliderHandler
    ) {
        $this->requestParser = $requestParser;
        $this->sliderHandler = $sliderHandler;
    }

    /**
     * @Route("/api/app-slider", name="admin.add_slider_api", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function insert(Request $request): JsonResponse
    {
        $slider = $this->requestParser->parse($request->request);

        $this->sliderHandler->save($slider);

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/set-sliders-position", name="admin.set_sliders_position", methods={"POST"}, options={"expose": true})
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function changeOrder(Request $request): JsonResponse
    {
        $this->sliderHandler->saveRowsPositions($request->request);

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/edit-slider/{id}", name="admin.edit_slider_api", methods={"PUT"}, options={"expose": true})
     * @param Request $request
     * @param Slider  $slider
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(Request $request, Slider $slider): JsonResponse
    {
        $slider = $this->requestParser->parse($request->request, $slider);

        $this->sliderHandler->save($slider);

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/toggle-slider-status/{id}/{status}", name="admin.api_toggle_slider_status", methods={"PATCH"},
     *                                                   options={"expose": true})
     *
     * @param Slider $slider
     * @param int    $status
     *
     * @return JsonResponse
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public function toggleActivation(Slider $slider, int $status): JsonResponse
    {
        $slider->setIsActive((bool) $status);

        $this->sliderHandler->save($slider);

        $statusText = ConstantsHelper::getConstantName((string) $status, 'STATUS', Slider::class);

        return $this->json(['text' => $statusText]);
    }
}