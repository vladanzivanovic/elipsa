<?php

declare(strict_types=1);

namespace App\Request\Dto;

use Nelexa\RequestDtoBundle\Dto\ConstructRequestObjectInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class ShopListRequestDto implements ConstructRequestObjectInterface
{

    public null|array $size = null;

    public null|array $color = null;

    public null|array $collection = null;

    public null|array $season = null;

    public null|array $attribute = null;

    public null|array $promotions = null;

    public null|array $price = null;

    public null|array $categories = null;

    public null|string $search = null;

    public function __construct(Request $request = null)
    {
        if (!$request instanceof Request) {
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
            'promotions' => $this->promotions,
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

        $this->setBasePart($body);

        $this->size =  isset($body['sizes']) ? explode('+', $body['sizes']) : null;
        $this->color = isset($body['colors']) ? explode('+', $body['colors']) : null;
        $this->collection = isset($body['collections']) ? explode('+', $body['collections']) : null;
        $this->season = isset($body['seasons']) ? explode('+', $body['seasons']) : null;
        $this->attribute = isset($body['attributes']) ? explode('+', $body['attributes']) : null;
        $this->promotions = isset($body['promotions']) ? explode('+', $body['promotions']) : null;
        $this->price = isset($body['price']) ? explode('+', $body['price']) : null;
        $this->categories = isset($body['categories']) ? explode('+', $body['categories']) : null;
    }

    private function setFromContent(Request $request): void
    {
        $body = json_decode($request->getContent(), true);

        $this->setBasePart($body);

        $this->size =  $body['sizes'] ?? null;
        $this->color = $body['colors'] ?? null;
        $this->collection = $body['collections'] ?? null;
        $this->season = $body['seasons'] ?? null;
        $this->attribute = $body['attributes'] ?? null;
        $this->promotions = $body['promotions'] ?? null;
        $this->price = $body['price'] ?? null;
        $this->categories = $body['categories'] ?? null;
    }

    /**
     * @param $body
     */
    private function setBasePart($body): void
    {
        $this->search = $body['search'] ?? null;
    }
}
