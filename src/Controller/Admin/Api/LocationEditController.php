<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Location;
use App\Handler\LocationHandler;
use App\Parser\LocationEditRequestParser;
use App\Request\Dto\Admin\LocationEditRequestDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class LocationEditController extends AbstractController
{
    public function __construct(
        private readonly LocationEditRequestParser $requestParser,
        private readonly LocationHandler $locationHandler
    ) {}

    /**
     *
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    #[Route(path: '/api/add-location', name: 'admin.add_location_api', methods: ['POST'])]
    public function insert(LocationEditRequestDto $locationEditRequestDto): JsonResponse
    {
        $banner = $this->requestParser->parse($locationEditRequestDto);

        $this->locationHandler->save($banner);

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    #[Route(path: '/api/edit-location/{id}', name: 'admin.edit_location_api', options: ['expose' => true], methods: ['PUT'])]
    public function update(LocationEditRequestDto $locationEditRequestDto, Location $location): JsonResponse
    {
        $location = $this->requestParser->parse($locationEditRequestDto, $location);

        $this->locationHandler->save($location);

        return $this->json(null, Response::HTTP_CREATED);
    }
}
