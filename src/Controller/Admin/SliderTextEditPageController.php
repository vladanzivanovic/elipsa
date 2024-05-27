<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\SliderText;
use App\Formatter\Admin\SliderTextEditResponseFormatter;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class SliderTextEditPageController extends AbstractController
{
    public function __construct(
        private readonly SliderTextEditResponseFormatter $responseFormatter
    ) {}

    /**
     *
     * @throws \ReflectionException
     */
    #[Route(path: '/add-slider-text', name: 'admin.add_slider_text_page', methods: ['GET'])]
    #[Template('Admin/Pages/sliderTextEdit.html.twig')]
    public function insert(): array
    {
        return $this->responseFormatter->formatResponse();
    }

    /**
     *
     * @throws \ReflectionException
     */
    #[Route(path: '/edit-slider-text/{id}', name: 'admin.edit_slider_text_page', methods: ['GET'])]
    #[Template('Admin/Pages/sliderTextEdit.html.twig')]
    public function update(SliderText $sliderText): array
    {
        return $this->responseFormatter->formatResponse($sliderText);
    }
}
