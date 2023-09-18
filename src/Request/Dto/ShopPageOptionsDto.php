<?php

declare(strict_types=1);

namespace App\Request\Dto;

use Nelexa\RequestDtoBundle\Dto\ConstructRequestObjectInterface;
use Symfony\Component\HttpFoundation\Request;

final class ShopPageOptionsDto implements ConstructRequestObjectInterface
{
    public const DEFAULT_MAX_PRODUCT_NUMBERS = 12;

    public ?string $sort = null;

    public int $limit = self::DEFAULT_MAX_PRODUCT_NUMBERS;

    public int $page = 1;

    public function __construct(Request $request = null)
    {
        if (null === $request) {
            return;
        }

        if (Request::METHOD_GET === $request->getMethod() && 0 < $request->query->count()) {
            $body = $request->query->all();
        }

        if (Request::METHOD_POST === $request->getMethod() && '' !== $request->getContent()) {
            $body = json_decode($request->getContent(), true);
        }

        if(isset($body['page'])) {
            $this->page = $body['page'];
        }

        $this->sort = $body['sort'] ?? null;
        $this->limit = isset($body['limit']) ? (int) $body['limit'] : self::DEFAULT_MAX_PRODUCT_NUMBERS;
    }

    public function toArray(): array
    {
        return [
            'sort' => $this->sort,
            'limit' => $this->limit,
            'page' => $this->page,
        ];
    }
}
