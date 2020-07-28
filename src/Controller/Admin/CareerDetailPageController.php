<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Career;
use App\Entity\ShopOrder;
use App\Formatter\Admin\CareerDetailResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;

final class CareerDetailPageController extends AbstractController
{
    /**
     * @var CareerDetailResponseFormatter
     */
    private $responseFormatter;
    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * @param ParameterBagInterface         $bag
     * @param CareerDetailResponseFormatter $responseFormatter
     */
    public function __construct(
        ParameterBagInterface $bag,
        CareerDetailResponseFormatter $responseFormatter
    ) {
        $this->responseFormatter = $responseFormatter;
        $this->bag = $bag;
    }

    /**
     * @Route("/career-detail/{id}", name="admin.view_career_details", methods={"GET"}, options={"expose": true})
     * @Template("Admin/Pages/careerDetail.html.twig")
     *
     * @param Career $career
     *
     * @return array
     */
    public function renderPage(Career $career): array
    {
        return $this->responseFormatter->formatResponse($career);
    }
}