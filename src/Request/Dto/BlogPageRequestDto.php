<?php

declare(strict_types=1);

namespace App\Request\Dto;

use Symfony\Component\HttpFoundation\Request;

final class BlogPageRequestDto extends AbstractRequestDto
{
    public null|string $slug = null;

    public function __construct(Request $request = null)
    {
        $this->slug = $request->attributes->get('slug');

        parent::__construct($request);
    }
}
