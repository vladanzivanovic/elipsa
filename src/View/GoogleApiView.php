<?php

declare(strict_types=1);

namespace App\View;

use Turanjanin\SerbianTransliterator\Transliterator;

final class GoogleApiView
{
    private Transliterator $transliterator;

    public function __construct(
        Transliterator $transliterator
    ) {
        $this->transliterator = $transliterator;
    }
    public function viewPlace(array $place): array
    {
        $terms = $place['terms'];

        $street = null;
        $city = null;
        $country = null;
        $addressText = null;

        switch (count($terms)) {
            case 2:
                $city = $this->transliterator->toLatin($terms[0]['value']);
                $country = $this->transliterator->toLatin($terms[1]['value']);
                $addressText = $city.', '.$country;
                break;
            case 3:
                $street = $this->transliterator->toLatin($terms[0]['value']);
                $city = $this->transliterator->toLatin($terms[1]['value']);
                $country = $this->transliterator->toLatin($terms[2]['value']);
                $addressText = $street.', '.$city.', '.$country;
                break;
        }

        return [
            'place_id' => $place['place_id'],
            'street' => $street,
            'city' => $city,
            'country' => $country,
            'address_text' => $addressText,
        ];
    }
}
