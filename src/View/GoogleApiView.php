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

        return [
            'place_id' => $place['place_id'],
            'street' => $this->transliterator->toLatin($terms[0]['value']),
            'city' => $this->transliterator->toLatin($terms[1]['value']),
            'country' => $this->transliterator->toLatin($terms[2]['value']),
        ];
    }
}
