<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Location;
use App\Formatter\Admin\LocationEditResponseFormatter;
use Symfony\Bridge\Twig\Attribute\Template;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class LocationEditPageController extends AbstractController
{
    private LocationEditResponseFormatter $responseFormatter;

    public function __construct(
        LocationEditResponseFormatter $responseFormatter
    ) {
        $this->responseFormatter = $responseFormatter;
    }

    #[Route(path: '/add-location', name: 'admin.add_location_page', methods: ['GET'])]
    #[Template('Admin/Pages/locationEdit.html.twig')]
    public function insert(): array
    {
        return $this->responseFormatter->formatResponse();
    }

    
    #[Route(path: '/edit-location/{id}', name: 'admin.edit_location_page', methods: ['GET'])]
    #[Template('Admin/Pages/locationEdit.html.twig')]
    public function update(Location $location): array
    {
        return $this->responseFormatter->formatResponse($location);
    }
}
