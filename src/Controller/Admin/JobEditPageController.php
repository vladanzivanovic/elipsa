<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Banner;
use App\Entity\CareerDescription;
use App\Formatter\Admin\JobEditResponseFormatter;
use Symfony\Bridge\Twig\Attribute\Template;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Attribute\Route;

final class JobEditPageController extends AbstractController
{
    private \App\Formatter\Admin\JobEditResponseFormatter $responseFormatter;

    public function __construct(
        ParameterBagInterface $bag,
        JobEditResponseFormatter $responseFormatter
    ) {
        $this->responseFormatter = $responseFormatter;
    }

    #[Route(path: '/add-job', name: 'admin.add_job', methods: ['GET'])]
    #[Template('Admin/Pages/jobEdit.html.twig')]
    public function insert(): array
    {
        return [];
    }

    
    #[Route(path: '/edit-job/{id}', name: 'admin.edit_job', methods: ['GET'])]
    #[Template('Admin/Pages/jobEdit.html.twig')]
    public function update(CareerDescription $careerDescription): array
    {
        return $this->responseFormatter->formatResponse($careerDescription);
    }
}
