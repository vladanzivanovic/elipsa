<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Career;
use App\Entity\ShopOrder;
use App\Formatter\Admin\CareerDetailResponseFormatter;
use Symfony\Bridge\Twig\Attribute\Template;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Attribute\Route;

final class CareerDetailPageController extends AbstractController
{
    private \App\Formatter\Admin\CareerDetailResponseFormatter $responseFormatter;

    public function __construct(
        ParameterBagInterface $bag,
        CareerDetailResponseFormatter $responseFormatter
    ) {
        $this->responseFormatter = $responseFormatter;
    }

    
    #[Route(path: '/career-detail/{id}', name: 'admin.view_career_details', methods: ['GET'], options: ['expose' => true])]
    #[Template('Admin/Pages/careerDetail.html.twig')]
    public function renderPage(Career $career): array
    {
        return $this->responseFormatter->formatResponse($career);
    }
}
