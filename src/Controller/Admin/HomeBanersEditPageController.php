<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Slider;
use App\Formatter\Admin\SliderEditResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;

final class HomeBanersEditPageController extends AbstractController
{
    /**
     * @var SliderEditResponseFormatter
     */
    private $responseFormatter;
    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * SliderEditPageController constructor.
     *
     * @param SliderEditResponseFormatter $responseFormatter
     * @param ParameterBagInterface       $bag
     */
    public function __construct(
        SliderEditResponseFormatter $responseFormatter,
        ParameterBagInterface $bag
    ) {
        $this->responseFormatter = $responseFormatter;
        $this->bag = $bag;
    }

    /**
     * @Route("/add-home-baner", name="admin.add_home_baner_page", methods={"GET"})
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
        $locales = explode('|', $this->bag->get('locales'));

        return $this->responseFormatter->formatResponse($slider);
    }
}