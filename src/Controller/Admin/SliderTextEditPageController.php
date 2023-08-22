<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\SliderText;
use App\Formatter\Admin\SliderTextEditResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;

final class SliderTextEditPageController extends AbstractController
{
    /**
     * @var SliderTextEditResponseFormatter
     */
    private $responseFormatter;

    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * @param SliderTextEditResponseFormatter $responseFormatter
     * @param ParameterBagInterface           $bag
     */
    public function __construct(
        SliderTextEditResponseFormatter $responseFormatter,
        ParameterBagInterface $bag
    ) {
        $this->responseFormatter = $responseFormatter;
        $this->bag = $bag;
    }

    /**
     * @Route("/add-slider-text", name="admin.add_slider_text_page", methods={"GET"})
     * @Template("Admin/Pages/sliderTextEdit.html.twig")
     *
     * @return array
     * @throws \ReflectionException
     */
    public function insert(): array
    {
        return $this->responseFormatter->formatResponse();
    }

    /**
     * @Route("/edit-slider-text/{id}", name="admin.edit_slider_text_page", methods={"GET"})
     * @Template("Admin/Pages/sliderTextEdit.html.twig")
     *
     * @param SliderText $sliderText
     *
     * @return array
     * @throws \ReflectionException
     */
    public function update(SliderText $sliderText): array
    {
        return $this->responseFormatter->formatResponse($sliderText);
    }
}
