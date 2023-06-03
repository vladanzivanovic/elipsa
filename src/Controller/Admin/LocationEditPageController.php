<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Location;
use App\Formatter\Admin\LocationEditResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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

    /**
     * @Route("/add-location", name="admin.add_location_page", methods={"GET"})
     * @Template("Admin/Pages/locationEdit.html.twig")
     *
     * @return array
     */
    public function insert(): array
    {
        return [];
    }

    /**
     * @Route("/edit-location/{id}", name="admin.edit_location_page", methods={"GET"})
     * @Template("Admin/Pages/locationEdit.html.twig")
     *
     * @param Location $location
     *
     * @return array
     */
    public function update(Location $location): array
    {
        return $this->responseFormatter->formatResponse($location);
    }
}