<?php

declare(strict_types=1);

namespace App\Request\Dto\Admin;

use Symfony\Component\HttpFoundation\Request;

final class ColorEditRequestDto extends AbstractEditRequestDto
{
    public string $hex;

    public function __construct(Request $request)
    {
        $body = $request->request;

        $this->hex = $body->get('hex');

        parent::__construct($request);
    }
}
