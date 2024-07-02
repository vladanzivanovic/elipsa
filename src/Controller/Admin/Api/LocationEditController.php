<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Location;
use App\Handler\LocationHandler;
use App\Parser\LocationEditRequestParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

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
    public function insert(Request $request): JsonResponse
    {
        $banner = $this->requestParser->parse($request->request);

        $this->locationHandler->save($banner);

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    #[Route(path: '/api/edit-location/{id}', name: 'admin.edit_location_api', options: ['expose' => true], methods: ['PUT'])]
    public function update(Request $request, Location $location): JsonResponse
    {
        $location = $this->requestParser->parse($request->request, $location);

        $this->locationHandler->save($location);

        return $this->json(null, Response::HTTP_CREATED);
    }
}
