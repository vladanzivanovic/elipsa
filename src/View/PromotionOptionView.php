<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Promotion;
use App\Entity\PromotionOption;

final class PromotionOptionView
{
    public function view(PromotionOption $promotionOption): array
    {
        $view = [
            'type' => $promotionOption->getType(),
            'configuration' => $promotionOption->getConfiguration(),
        ];

        return $view;
    }
}
