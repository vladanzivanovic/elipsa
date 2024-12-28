<?php

declare(strict_types=1);

namespace App\Request\Dto\Admin;

use App\Entity\PromotionOption;

class PromotionOptionRequestDto
{
    public ?array $categories = null;

    public ?array $tags = null;

    public ?array $products = null;

    public ?array $colors = null;

    public bool $applicableAllProducts = false;

    public function __construct(?array $body = null)
    {
        $this->categories = $body[PromotionOption::OPTION_CATEGORIES] ?? null;
        $this->tags = $body[PromotionOption::OPTION_TAGS] ?? null;
        $this->products = $body[PromotionOption::OPTION_PRODUCTS] ?? null;
        $this->colors = $body[PromotionOption::OPTION_COLORS] ?? null;
        $this->applicableAllProducts = isset($body[PromotionOption::OPTION_ALL_PRODUCTS]);
    }

    public function toArray(): array
    {
        return [
            PromotionOption::OPTION_CATEGORIES => $this->categories,
            PromotionOption::OPTION_TAGS => $this->tags,
            PromotionOption::OPTION_PRODUCTS => $this->products,
            PromotionOption::OPTION_COLORS => $this->colors,
            PromotionOption::OPTION_ALL_PRODUCTS => $this->applicableAllProducts,
        ];
    }
}
