<?php

declare(strict_types=1);

namespace App\Request\Dto;

use Symfony\Component\HttpFoundation\Request;

final class OrderProductRequestDto extends OrderRequestDto
{
    public string $size;

    public int $color;

    public int $quantity;

    public string $productSlug;

    public function __construct(Request $request)
    {
        $body = json_decode($request->getContent(), true);

        $this->size = (string) $body['size'];
        $this->color = $body['color'];
        $this->quantity = $body['quantity'] ? (int) $body['quantity'] : null;
        $this->productSlug = $request->attributes->get('slug');

        parent::__construct($request);
    }
}
