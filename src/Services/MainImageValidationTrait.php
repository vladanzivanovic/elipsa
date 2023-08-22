<?php

declare(strict_types=1);

namespace App\Services;

trait MainImageValidationTrait
{
    /**
     * @param array $data
     *
     * @return bool
     */
    private function validateMainImage(array $data): bool
    {
        foreach ($data as $image) {
            if (true === !!$image['isMain']) {
                return true;
            }
        }

        return false;
    }
}
