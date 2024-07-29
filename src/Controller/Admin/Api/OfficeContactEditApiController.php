<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\OfficeContact;
use App\Handler\OfficeContactHandler;
use App\Parser\OfficeContactParser;
use App\Request\Dto\Admin\OfficeContactEditRequestDto;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OfficeContactEditApiController extends AbstractController
{
    public function __construct(
        private readonly OfficeContactParser $officeContactParser,
        private readonly OfficeContactHandler $officeContactHandler
    ) {}

    /**
     * @throws \Exception
     */
    #[Route(path: '/api/add-office-contact', name: 'admin.add_office_contact_api', options: ['expose' => true], methods: ['POST'])]
    public function insert(OfficeContactEditRequestDto $officeContactEditRequestDto): JsonResponse
    {
        $officeContact = $this->officeContactParser->parse($officeContactEditRequestDto);

        $this->officeContactHandler->save($officeContact);

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     * @throws \Exception
     */
    #[Route(path: '/api/edit-office-contact/{id}', name: 'admin.edit_office_contact_api', options: ['expose' => true], methods: ['PUT'])]
    public function update(OfficeContactEditRequestDto $officeContactEditRequestDto, OfficeContact $officeContact): JsonResponse
    {
        $officeContact = $this->officeContactParser->parse($officeContactEditRequestDto, $officeContact);

        $this->officeContactHandler->save($officeContact);

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     *
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[Route(path: '/api/remove-office-contact/{id}', name: 'admin.remove_office_contact_api', options: ['expose' => true], methods: ['DELETE'])]
    public function remove(OfficeContact $officeContact): JsonResponse
    {
        $this->officeContactHandler->remove($officeContact);

        return $this->json(null);
    }
}
