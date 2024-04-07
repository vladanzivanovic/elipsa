<?php

namespace App;

use Symfony\Component\HttpFoundation\ParameterBag;

trait ShopTrait
{
    private function parseSearchData(string $searchData): ParameterBag
    {
        $searchArray = explode('/', $searchData);
        $filters = [];
        $criteria = [];
        $sortMapper = $this->bag->get('shop')['sort_mapping'];
        $counter = count($searchArray);

        for ($i = 0; $i < $counter; $i++) {
            if ($i % 2 == 0) {
                $filters[] = $this->translator->trans($searchArray[$i], [], 'messages', 'en');

                continue;
            }

            $value = explode('+', $searchArray[$i]);

            if (end($filters) === 'sort') {
                $value = $sortMapper[$value[0]];
            }

            $criteria[] = $value;
        }

        return new ParameterBag(array_combine($filters, $criteria));
    }
}
