<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\OfficeContact;
use App\Formatter\Admin\OfficeContactEditResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class OfficeContactEditPageController extends AbstractController
{
    private OfficeContactEditResponseFormatter $OfficeContactEditResponseFormatter;

    public function __construct(
        OfficeContactEditResponseFormatter $OfficeContactEditResponseFormatter
    ) {

        $this->OfficeContactEditResponseFormatter = $OfficeContactEditResponseFormatter;
    }
    /**
     * @Route("/add-office-contact", name="admin.add_office_contact_page", methods={"GET"})
     * @Template("Admin/Pages/officeContactEdit.html.twig")
     *
     * @return array
     */
    public function insert(): array
    {
        return $this->OfficeContactEditResponseFormatter->formatResponse();
    }

    /**
     * @Route("/edit-office-contact/{id}", name="admin.edit_office_contact_page", methods={"GET"})
     * @Template("Admin/Pages/OfficeContactEdit.html.twig")
     *
     */
    public function update(OfficeContact $officeContact): array
    {
        return $this->OfficeContactEditResponseFormatter->formatResponse($officeContact);
    }
}
