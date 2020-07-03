<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Location;
use App\Handler\LocationHandler;
use App\Parser\LocationEditRequestParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class LocationEditController extends AbstractController
{
    /**
     * @var LocationEditRequestParser
     */
    private $requestParser;

    /**
     * @var LocationHandler
     */
    private $locationHandler;

    /**
     * @param LocationEditRequestParser $requestParser
     * @param LocationHandler           $locationHandler
     */
    public function __construct(
        LocationEditRequestParser $requestParser,
        LocationHandler $locationHandler
    ) {
        $this->requestParser = $requestParser;
        $this->locationHandler = $locationHandler;
    }

    /**
     * @Route("/api/add-location", name="admin.add_location_api", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    public function insert(Request $request): JsonResponse
    {
        $banner = $this->requestParser->parse($request->request);

        $this->locationHandler->save($banner);

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/edit-location/{id}", name="admin.edit_location_api", methods={"PUT"}, options={"expose": true})
     * @param Request  $request
     * @param Location $location
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     */
    public function update(Request $request, Location $location): JsonResponse
    {
        $location = $this->requestParser->parse($request->request, $location);

        $this->locationHandler->save($location);

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }
}