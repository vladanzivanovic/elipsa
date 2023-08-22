<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Slider;
use App\Formatter\Admin\SliderEditResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class SliderEditPageController extends AbstractController
{
    private SliderEditResponseFormatter $responseFormatter;

    public function __construct(
        SliderEditResponseFormatter $responseFormatter
    ) {
        $this->responseFormatter = $responseFormatter;
    }

    /**
     * @Route("/add-slider", name="admin.add_slider_page", methods={"GET"})
     * @Template("Admin/Pages/sliderEdit.html.twig")
     *
     * @return array
     */
    public function insert(): array
    {
        return [];
    }

    /**
     * @Route("/edit-slider/{id}", name="admin.edit_slider_page", methods={"GET"})
     * @Template("Admin/Pages/sliderEdit.html.twig")
     *
     * @param Slider $slider
     *
     * @return array
     */
    public function update(Slider $slider): array
    {
        return $this->responseFormatter->formatResponse($slider);
    }
}
