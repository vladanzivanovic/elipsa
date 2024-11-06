<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\OfficeContact;
use App\Formatter\Admin\OfficeContactEditResponseFormatter;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

final class OfficeContactEditPageController extends AbstractController
{
    public function __construct(
        private readonly OfficeContactEditResponseFormatter $officeContactEditResponseFormatter
    ) {}

    #[Route(path: '/add-office-contact', name: 'admin.add_office_contact_page', methods: ['GET'])]
    #[Template('Admin/Pages/officeContactEdit.html.twig')]
    public function insert(): array
    {
        return $this->officeContactEditResponseFormatter->formatResponse();
    }

    
    #[Route(path: '/edit-office-contact/{id}', name: 'admin.edit_office_contact_page', methods: ['GET'])]
    #[Template('Admin/Pages/officeContactEdit.html.twig')]
    public function update(OfficeContact $officeContact): array
    {
        return $this->officeContactEditResponseFormatter->formatResponse($officeContact);
    }
}
