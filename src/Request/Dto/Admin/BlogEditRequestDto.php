<?php

declare(strict_types=1);

namespace App\Request\Dto\Admin;

use Symfony\Component\HttpFoundation\Request;

final class BlogEditRequestDto extends AbstractEditRequestDto
{
    public array $tags;

    public function __construct(Request $request)
    {
        $body = $request->request;

        $this->tags = $body->all('tags');

        parent::__construct($request);
    }
}
