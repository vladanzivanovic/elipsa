<?php

declare(strict_types=1);

namespace App\Provider;

use App\Request\Dto\GoogleApiRequestDto;
use App\View\GoogleApiView;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Turanjanin\SerbianTransliterator\Transliterator;

final class GoogleApiProvider
{
    public const TYPE_ADDRESS = 'route';
    public const TYPE_CITY = 'locality';

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
    public function getAddresses(GoogleApiRequestDto $googleApiRequestDto): array
    {
        $response = $this->client->request(
            'GET',
            self::PLACE_API,
            [
                'query' => [
                    'input' => $googleApiRequestDto->query,
                    'language' => 'sr_RS',
                    'components' => 'country:rs|country:ba',
                    'key' => $this->googleApiKey,
                    'types' => $googleApiRequestDto->type
                ],
            ]
        );

        $content = json_decode($response->getContent(), true);

        dd($content);

        $places = [];

        foreach ($content['predictions'] as $place) {
            $places[] = $this->googleApiView->viewPlace($place);
        }

        return $places;
    }
}
