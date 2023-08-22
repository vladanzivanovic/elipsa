<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\OfficeContact;
use App\Handler\OfficeContactHandler;
use App\Parser\OfficeContactParser;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class OfficeContactEditApiController extends AbstractController
{
    private OfficeContactParser $OfficeContactParser;

    private OfficeContactHandler $officeContactHandler;

    public function __construct(
        OfficeContactParser $OfficeContactParser,
        OfficeContactHandler $OfficeContactHandler
    ) {
        $this->OfficeContactParser = $OfficeContactParser;
        $this->officeContactHandler = $OfficeContactHandler;
    }

    /**
     * @Route("/api/add-office-contact", name="admin.add_office_contact_api", methods={"POST"}, options={"expose": true})

     * @throws \Exception
     */
    public function insert(Request $request): JsonResponse
    {
        $officeContact = $this->OfficeContactParser->parse($request->request);

        $this->officeContactHandler->save($officeContact);

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/edit-office-contact/{id}", name="admin.edit_office_contact_api", methods={"PUT"}, options={"expose": true})
     * @throws \Exception
     */
    public function update(Request $request, OfficeContact $officeContact): JsonResponse
    {
        $officeContact = $this->OfficeContactParser->parse($request->request, $officeContact);

        $this->officeContactHandler->save($officeContact);

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/remove-office-contact/{id}", name="admin.remove_office_contact_api", methods={"DELETE"}, options={"expose": true})
     *
     * @param OfficeContact $officeContact
     *
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(OfficeContact $officeContact): JsonResponse
    {
        $this->officeContactHandler->remove($officeContact);

        return $this->json(null);
    }
}
