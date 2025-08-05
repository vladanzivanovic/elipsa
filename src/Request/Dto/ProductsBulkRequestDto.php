<?php

declare(strict_types=1);

namespace App\Request\Dto;

use Nelexa\RequestDtoBundle\Dto\ConstructRequestObjectInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductsBulkRequestDto implements ConstructRequestObjectInterface
{
    public array $products;

    public ?int $discount = null;

    public ?string $country = null;

    public function __construct(Request $request = null)
    {
        if (!$request instanceof \Symfony\Component\HttpFoundation\Request) {
            return;
        }

        $body = json_decode($request->getContent(), true);

        $this->products = $body['ids'];

        if (isset($body['discount'])) {
            $this->discount = $body['discount'];
        }

        if (true === $request->attributes->has('country')) {
            $this->country = $request->attributes->get('country');
        }
    }
}
