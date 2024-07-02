<?php

declare(strict_types=1);

namespace App\Request\Dto\Admin;

use Symfony\Component\HttpFoundation\Request;

final class SizeEditRequestDto extends AbstractEditRequestDto
{
    public string $size;

    public function __construct(Request $request)
    {
        $body = $request->request;

        $this->size = $body->get('size');

        parent::__construct($request);
    }
}
