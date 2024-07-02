<?php

declare(strict_types=1);

namespace App\Request\Dto\Admin;

use Symfony\Component\HttpFoundation\Request;

final class CategoryEditRequestDto extends AbstractEditRequestDto
{
    public null|string $parent = null;

    public function __construct(Request $request)
    {
        $body = $request->request;

        $this->parent = $body->get('parent_category');

        parent::__construct($request);
    }
}
