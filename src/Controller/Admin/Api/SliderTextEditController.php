<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Resources\StatusInterface;
use App\Entity\Slider;
use App\Entity\SliderText;
use App\Handler\SliderHandler;
use App\Handler\SliderTextHandler;
use App\Helper\ConstantsHelper;
use App\Parser\SliderEditRequestParser;
use App\Parser\SliderTextEditRequestParser;
use App\Request\Dto\Admin\SliderTextEditRequestDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SliderTextEditController extends AbstractController
{
    public function __construct(
        private readonly SliderTextEditRequestParser $requestParser,
        private readonly SliderTextHandler $handler
    ) {}

    /**
     *
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    #[Route(path: '/api/app-slider-text', name: 'admin.add_slider_text_api', options: ['expose' => true], methods: ['POST'])]
    public function insert(SliderTextEditRequestDto $sliderTextEditRequestDto): JsonResponse
    {
        $sliderText = $this->requestParser->parse($sliderTextEditRequestDto);

        $this->handler->save($sliderText);

        return $this->json(null, Response::HTTP_CREATED);
    }

    
    #[Route(path: '/api/edit-slider-text/{id}', name: 'admin.edit_slider_text_api', options: ['expose' => true], methods: ['PUT'])]
    public function update(SliderTextEditRequestDto $sliderTextEditRequestDto, SliderText $sliderText): JsonResponse
    {
        $sliderText = $this->requestParser->parse($sliderTextEditRequestDto, $sliderText);

        $this->handler->save($sliderText);

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     *
     *
     * @throws \ReflectionException
     */
    #[Route(path: '/api/toggle-slider-text-status/{id}/{status}', name: 'admin.api_toggle_slider_text_status', options: ['expose' => true], methods: ['PATCH'])]
    public function toggleActivation(SliderText $sliderText, string $status): JsonResponse
    {
        $sliderText->setStatus($status);

        $this->handler->save($sliderText);

        $statusText = ConstantsHelper::getConstantName((string) $status, 'STATUS', StatusInterface::class);

        return $this->json(['text' => $statusText]);
    }
}
