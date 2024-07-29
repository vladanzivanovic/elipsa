<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\SliderText;
use App\Handler\SliderTextHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class SliderTextRemoveController extends AbstractController
{
    private SliderTextHandler $handler;

    public function __construct(
        SliderTextHandler $handler
    ) {
        $this->handler = $handler;
    }

    /**
     *
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    #[Route(path: '/api/remove-slider-text/{id}', name: 'admin.remove_slider_text_api', methods: ['DELETE'], options: ['expose' => true])]
    public function remove(SliderText $sliderText): JsonResponse
    {
        $this->handler->remove($sliderText);

        return $this->json(null);
    }
}
