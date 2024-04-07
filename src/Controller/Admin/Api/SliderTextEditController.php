<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Slider;
use App\Entity\SliderText;
use App\Handler\SliderHandler;
use App\Handler\SliderTextHandler;
use App\Helper\ConstantsHelper;
use App\Parser\SliderEditRequestParser;
use App\Parser\SliderTextEditRequestParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SliderTextEditController extends AbstractController
{
    private SliderTextEditRequestParser $requestParser;

    private SliderTextHandler $handler;

    public function __construct(
        SliderTextEditRequestParser $requestParser,
        SliderTextHandler $handler
    ) {
        $this->requestParser = $requestParser;
        $this->handler = $handler;
    }

    /**
     *
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    #[Route(path: '/api/app-slider-text', name: 'admin.add_slider_text_api', options: ['expose' => true], methods: ['POST'])]
    public function insert(Request $request): JsonResponse
    {
        $sliderText = $this->requestParser->parse($request->request);

        $this->handler->save($sliderText);

        return $this->json(null, Response::HTTP_CREATED);
    }

    
    #[Route(path: '/api/edit-slider-text/{id}', name: 'admin.edit_slider_text_api', methods: ['PUT'], options: ['expose' => true])]
    public function update(Request $request, SliderText $sliderText): JsonResponse
    {
        $sliderText = $this->requestParser->parse($request->request, $sliderText);

        $this->handler->save($sliderText);

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     *
     *
     * @throws \ReflectionException
     */
    #[Route(path: '/api/toggle-slider-text-status/{id}/{status}', name: 'admin.api_toggle_slider_text_status', methods: ['PATCH'], options: ['expose' => true])]
    public function toggleActivation(SliderText $sliderText, int $status): JsonResponse
    {
        $sliderText->setIsActive((bool) $status);

        $this->handler->save($sliderText);

        $statusText = ConstantsHelper::getConstantName((string) $status, 'STATUS', Slider::class);

        return $this->json(['text' => $statusText]);
    }
}
