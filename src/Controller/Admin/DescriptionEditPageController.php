<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Formatter\Admin\DescriptionEditResponseFormatter;
use Symfony\Bridge\Twig\Attribute\Template;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

final class DescriptionEditPageController extends AbstractController
{
    private DescriptionEditResponseFormatter $responseFormatter;

    public function __construct(
        DescriptionEditResponseFormatter $responseFormatter
    ) {
        $this->responseFormatter = $responseFormatter;
    }

    #[Route(path: '/add-description', name: 'admin.add_description_page', methods: ['GET'])]
    #[Template('Admin/Pages/descriptionEdit.html.twig')]
    public function insert(): array
    {
        return $this->responseFormatter->formatResponse();
    }

    #[Route(path: '/edit-description/{type}', name: 'admin.edit_description_page', methods: ['GET'])]
    #[Template('Admin/Pages/descriptionEdit.html.twig')]
    public function update(string $type): array
    {
        return $this->responseFormatter->formatResponse($type);
    }
}
