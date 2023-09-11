<?php

declare(strict_types=1);

namespace App\Request\Dto;

use Nelexa\RequestDtoBundle\Dto\ConstructRequestObjectInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class ShopListRequestDto implements ConstructRequestObjectInterface
{

    public ?array $size = null;

    public ?array $color = null;

    public ?array $collection = null;

    public ?array $season = null;

    public ?array $attribute = null;

    public ?array $price = null;

    public ?array $categories = null;

    public ?string $search = null;

    public function __construct(Request $request = null)
    {
        if (null === $request) {
            return;
        }

        if (Request::METHOD_GET === $request->getMethod() && 0 < $request->query->count()) {
            $this->setFromQuery($request);
        }

        if (Request::METHOD_POST === $request->getMethod() && '' !== $request->getContent()) {
            $this->setFromContent($request);
        }
    }

    public function toArray(): array
    {
        return [
            'sizes' => $this->size,
            'categories' => $this->categories,
            'collections' => $this->collection,
            'seasons' => $this->season,
            'attributes' => $this->attribute,
            'price' => $this->price,
            'colors' => $this->color,
            'search' => $this->search,
        ];
    }

    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
    }

    private function setFromQuery(Request $request): void
    {
        $body = $request->query->all();

        $this->setBasePart($body, $request);

        $this->size =  isset($body['sizes']) ? explode('+', $body['sizes']) : null;
        $this->color = isset($body['colors']) ? explode('+', $body['colors']) : null;
        $this->collection = isset($body['collections']) ? explode('+', $body['collections']) : null;
        $this->season = isset($body['seasons']) ? explode('+', $body['seasons']) : null;
        $this->attribute = isset($body['attributes']) ? explode('+', $body['attributes']) : null;
        $this->price = isset($body['price']) ? explode('+', $body['price']) : null;
        $this->categories = isset($body['categories']) ? explode('+', $body['categories']) : null;
    }

    private function setFromContent(Request $request): void
    {
        $body = json_decode($request->getContent(), true);

        $this->setBasePart($body, $request);

        $this->size =  $body['sizes'] ?? null;
        $this->color = $body['colors'] ?? null;
        $this->collection = $body['collections'] ?? null;
        $this->season = $body['seasons'] ?? null;
        $this->attribute = $body['attributes'] ?? null;
        $this->price = $body['price'] ?? null;
        $this->categories = $body['categories'] ?? null;
    }

    /**
     * @param $body
     * @param Request $request
     * @return void
     */
    private function setBasePart($body, Request $request): void
    {
        $this->search = $body['search'] ?? null;
    }
}
