<?php

declare(strict_types=1);

namespace App\Controller\Unique;

use App\Provider\GoogleApiProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class GoogleApiController extends AbstractController
{
    private GoogleApiProvider $googleApiProvider;

    public function __construct(
        GoogleApiProvider $googleApiProvider
    ) {
        $this->googleApiProvider = $googleApiProvider;
    }

    /**
     * @return JsonResponse
     */
    #[Route(path: '/api/place/{query}', name: 'app_api.place_search', methods: ['GET'], options: ['expose' => true])]
    public function placeSearch(string $query): Response
    {
        $places = $this->googleApiProvider->getAddresses($query);

        return $this->json($places, Response::HTTP_OK);
    }
}
