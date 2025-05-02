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

    public bool $homeScreenBar = false;

    public function __construct(?array $body = null)
    {
        $this->categories = $body[PromotionOption::RULE_CATEGORIES] ?? null;
        $this->tags = $body[PromotionOption::RULE_TAGS] ?? null;
        $this->products = $body[PromotionOption::RULE_PRODUCTS] ?? null;
        $this->colors = $body[PromotionOption::RULE_COLORS] ?? null;
        $this->applicableAllProducts = isset($body[PromotionOption::RULE_ALL_PRODUCTS]);
        $this->homeScreenBar = isset($body[PromotionOption::OPTION_HOME_SCREEN_BAR]);
    }

    public function toArray(): array
    {
        return [
            PromotionOption::RULE_CATEGORIES => $this->categories,
            PromotionOption::RULE_TAGS => $this->tags,
            PromotionOption::RULE_PRODUCTS => $this->products,
            PromotionOption::RULE_COLORS => $this->colors,
            PromotionOption::RULE_ALL_PRODUCTS => $this->applicableAllProducts,
            PromotionOption::OPTION_HOME_SCREEN_BAR => $this->homeScreenBar,
        ];
    }
}
