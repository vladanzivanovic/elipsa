<?php

declare(strict_types=1);

namespace App\Request\Dto\Admin;

use App\Entity\Tags;
use Symfony\Component\HttpFoundation\Request;

final class TagEditRequestDto extends AbstractEditRequestDto
{
    public null|string $productType = null;

    public int $tagType;

    public function __construct(Request $request)
    {
        $body = $request->request;

        $this->productType = $body->get('product_type');
        $this->tagType = !str_contains($request->attributes->get('_route'), 'blog_tag_api') ?
            Tags::TYPE_PRODUCT : Tags::TYPE_BLOG;

        parent::__construct($request);
    }
}
