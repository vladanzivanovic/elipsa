<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Slider;
use App\Formatter\Admin\SliderEditResponseFormatter;
use Symfony\Bridge\Twig\Attribute\Template;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

final class SliderEditPageController extends AbstractController
{
    public function __construct(
        private readonly SliderEditResponseFormatter $responseFormatter
    ) {}

    #[Route(path: '/add-slider', name: 'admin.add_slider_page', methods: ['GET'])]
    #[Template('Admin/Pages/sliderEdit.html.twig')]
    public function insert(): array
    {
        return [];
    }

    
    #[Route(path: '/edit-slider/{id}', name: 'admin.edit_slider_page', methods: ['GET'])]
    #[Template('Admin/Pages/sliderEdit.html.twig')]
    public function update(Slider $slider): array
    {
        return $this->responseFormatter->formatResponse($slider);
    }
}
