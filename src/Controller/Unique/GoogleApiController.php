<?php

declare(strict_types=1);

namespace App\Controller\Unique;

use App\Provider\GoogleApiProvider;
use App\Request\Dto\GoogleApiRequestDto;
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
     * @Route("/api/place/autocomplete",
     *     name="app_api.place_search",
     *     methods={"GET"},
     *     options={"expose": true}
     * )
     *
     * @return JsonResponse
     */
    public function placeSearch(GoogleApiRequestDto $googleApiRequestDto): Response
    {
        $places = $this->googleApiProvider->getAddresses($googleApiRequestDto);

        return $this->json($places, Response::HTTP_OK);
    }
}
