<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\SliderText;
use App\Handler\SliderTextHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class SliderTextRemoveController extends AbstractController
{
    /**
     * @var SliderTextHandler
     */
    private $handler;

    /**
     * @param SliderTextHandler $handler
     */
    public function __construct(
        SliderTextHandler $handler
    ) {
        $this->handler = $handler;
    }

    /**
     * @Route("/api/remove-slider-text/{id}", name="admin.remove_slider_text_api", methods={"DELETE"}, options={"expose": true})
     *
     * @param SliderText $sliderText
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(SliderText $sliderText): JsonResponse
    {
        $this->handler->remove($sliderText);

        return $this->json(null);
    }
}