<?php

declare(strict_types=1);

namespace App\Services;

trait MainImageValidationTrait
{
    
    private function validateMainImage(array $data): bool
    {
        foreach ($data as $image) {
            if ((bool) $image['isMain']) {
                return true;
            }
        }

        return false;
    }
}
