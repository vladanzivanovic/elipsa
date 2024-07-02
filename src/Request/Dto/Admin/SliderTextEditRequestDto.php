<?php

declare(strict_types=1);

namespace App\Request\Dto\Admin;

use Symfony\Component\HttpFoundation\Request;

final class SliderTextEditRequestDto extends AbstractEditRequestDto
{
    public string $position;

    public function __construct(Request $request)
    {
        $body = $request->request;

        $this->position = $body->get('position');
        parent::__construct($request);
    }
}
