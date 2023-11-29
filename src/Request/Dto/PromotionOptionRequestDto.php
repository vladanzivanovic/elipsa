<?php

declare(strict_types=1);

namespace App\Request\Dto;

class PromotionOptionRequestDto
{
    public ?array $categories = null;

    public ?array $tags = null;

    public ?array $products = null;

    public bool $applicableAllProducts = false;

    public function __construct(?array $body = null)
    {
        $this->categories = $body['categories'] ?? null;
        $this->tags = $body['tags'] ?? null;
        $this->products = $body['products'] ?? null;
        $this->applicableAllProducts = isset($body['applicable_all_products']);
    }

    public function toArray(): array
    {
        return [
            'categories' => $this->categories,
            'tags' => $this->tags,
            'products' => $this->products,
            'applicable_all_products' => $this->applicableAllProducts,
        ];
    }
}
