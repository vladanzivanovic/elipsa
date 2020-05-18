<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ProductColor;
use App\Repository\ProductColorRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;

final class ColorEditPageController extends AbstractController
{
    /**
     * @var ProductColorRepository
     */
    private $colorRepository;
    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * ColorEditPageController constructor.
     *
     * @param ProductColorRepository $colorRepository
     * @param ParameterBagInterface  $bag
     */
    public function __construct(
        ProductColorRepository $colorRepository,
        ParameterBagInterface $bag
    ) {
        $this->colorRepository = $colorRepository;
        $this->bag = $bag;
    }

    /**
     * @Route("/add-color", name="admin.add_color_page", methods={"GET"})
     * @Template("Admin/Pages/colorEdit.html.twig")
     *
     * @return array
     */
    public function insert(): array
    {
        return [];
    }

    /**
     * @Route("/edit-color/{id}", name="admin.edit_color_page", methods={"GET"})
     * @Template("Admin/Pages/colorEdit.html.twig")
     *
     * @param ProductColor $color
     *
     * @return array
     */
    public function update(ProductColor $color): array
    {
        $colors = $this->colorRepository->getByColorForAdmin($color);

        return $colors[0];
    }
}