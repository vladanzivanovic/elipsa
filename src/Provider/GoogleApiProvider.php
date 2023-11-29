<?php

declare(strict_types=1);

namespace App\Provider;

use App\Controller\View\GoogleApiView;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Turanjanin\SerbianTransliterator\Transliterator;

final class GoogleApiProvider
{
    private const PLACE_API = 'https://maps.googleapis.com/maps/api/place/autocomplete/json';

    private HttpClientInterface $client;

    private Transliterator $transliterator;

    private GoogleApiView $googleApiView;

    private string $googleApiKey;

    public function __construct(
        HttpClientInterface $client,
        Transliterator $transliterator,
        GoogleApiView $googleApiView,
        string $googleApiKey
    ) {
        $this->client = $client;
        $this->googleApiKey = $googleApiKey;
        $this->transliterator = $transliterator;
        $this->googleApiView = $googleApiView;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getAddresses($query): array
    {
        $response = $this->client->request(
            'GET',
            self::PLACE_API,
            [
                'query' => [
                    'input' => $query,
                    'language' => 'sr_RS',
                    'components' => 'country:rs|country:ba',
                    'key' => $this->googleApiKey
                ],
            ]
        );

        $content = json_decode($response->getContent(), true);

        $places = [];

        foreach ($content['predictions'] as $place) {
            $places[] = $this->googleApiView->viewPlace($place);
        }

        return $places;
    }
}
